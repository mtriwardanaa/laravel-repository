<?php

namespace App\Helper\Repository;

use App\Helper\Ddd\AbstractModel;
use App\Helper\Ddd\ModelInterface;
use App\Helper\ProjectionRequest;
use App\Helper\Trait\ConfigPathTrait;
use App\Helper\Ddd\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;

/**
 * Summary of BaseRepository
 * @author PutrimakIslan
 * @copyright (c) 2023
 */
class BaseRepository
{
    use ConfigPathTrait;

    const PESSIMISTIC_WRITE = 1;
    const PESSIMISTIC_READ = 2;

    protected $modelClass;
    protected $pageSize;
    protected $locking;
    protected $relations = [];

    protected $request;

    public function __construct(Request $request)
    {
        $this->setModelClass($this->getModelPath());
        $this->request = $request;
    }

    public function setModelClass(string $modelClass)
    {
        return $this->modelClass = app($modelClass);
    }

    public function lock(int $lockMode): void
    {
        $this->locking = $lockMode;
    }

    public function paginate(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    public function with(array $withs): void
    {
        $this->relations = array_merge($this->relations, $withs);
    }

    /**
     * @param mixed    $id
     * @param int|null $lockMode
     *
     * @return AbstractModel|mixed|null
     */
    public function find($id, int $lockMode = null)
    {
        if ($id instanceof SerializableInterface) {
            $id = $id->serialize();
        }

        $qb = $this->createQueryBuilder();
        $qb->whereKey($id);

        switch ($lockMode) {
            case self::PESSIMISTIC_WRITE:
                $qb->lockForUpdate();
                break;
            case self::PESSIMISTIC_READ:
                $qb->sharedLock();
                break;
            default:
                $qb->sharedLock();
                break;
        }

        return $this->executeSingle($qb);
    }

    /**
     * @param array $criteria
     *
     * @return AbstractModel|mixed
     */
    public function findOneBy(array $criteria)
    {
        $stmt = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            $stmt->where($key, $value);
        }

        return $this->executeSingle($stmt);
    }

    /**
     * @param array $criteria
     *
     * @return Collection|LengthAwarePaginator|AbstractModel[]|mixed
     */
    public function findBy(array $criteria)
    {
        $stmt = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            $stmt->where($key, $value);
        }

        return $this->execute($stmt);
    }

    /**
     * @return LengthAwarePaginator|mixed|AbstractModel[]|Collection
     */
    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * @param string|null $modelClass
     *
     * @return Builder|QueryBuilder
     */
    protected function createQueryBuilder(string $modelClass = null)
    {
        $model = $this->createModel($modelClass ?: $this->modelClass);
        return $model->newQuery();
    }

    /**
     * Create model.
     *
     * @param string $modelClass
     *
     * @return AbstractModel|ModelInterface
     */
    protected function createModel(Model $modelClass)
    {
        return new $modelClass;
    }

    protected function executeSingle($query)
    {
        return $query->select($this->projection())->first();
    }

    protected function execute($query)
    {
        $query->select($this->projection());
        if (null !== $this->pageSize) {
            $pageSize = $this->pageSize;
            $this->pageSize = null;

            return $query->paginate($pageSize);
        }

        return $query->get();
    }

    private function projection()
    {
        if ($this->request->get('size')) {
            $this->paginate($this->request->get('size'));
        }

        $project = new ProjectionRequest($this->request, new($this->getModelPath()));
        return $project->projection();
    }

    protected function save($payload)
    {
        // return
    }
}
