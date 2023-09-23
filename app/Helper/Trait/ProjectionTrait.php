<?php

namespace App\Helper\Trait;

/**
 * Summary of ProjectionTrait
 * @author PutrimakIslan
 * @copyright (c) 2023
 */
trait ProjectionTrait
{
    private string $regex = '/[^,_a-zA-Z]/';
    private $request;
    private $table;

    public function __construct($request, $table)
    {
        $this->request = $request;
        $this->table = $table;
    }

    public function projection()
    {
        return array_values(array_diff(array_merge($this->table->project, $this->fieldAdd()), $this->fieldDelete()));
    }

    public function childProjection($childTable, $key)
    {
        return array_values(
            array_diff(
                array_merge(
                    $childTable->project,
                    $this->fieldAddChild($key)
                ),
                $this->fieldDeleteChild($key)
            )
        );
    }

    private function execute($field)
    {
        $fieldTrim = preg_replace($this->regex, '', $field);
        $fieldImplode = explode(',', $fieldTrim);
        return array_unique($fieldImplode);
    }

    public function camelToSnake($str)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', preg_replace('/[^a-zA-Z]+/', '', $str)));
    }

    public function fieldAdd()
    {
        $field = $this->request->get('field-add');
        $fieldFilter = array();
        if (!empty($field)) {
            $fieldFilter = $this->execute($field);
        }

        return $fieldFilter;
    }

    public function fieldDelete()
    {
        $field = $this->request->get('field-del');
        $fieldFilter = array();
        if (!empty($field)) {
            $fieldFilter = $this->execute($field);
        }

        return $fieldFilter;
    }

    public function fieldAddChild($key)
    {
        $trim = $this->camelToSnake(preg_replace($this->regex, '', $key));
        $field = $this->request->get('field-add-' . $trim);
        $fieldFilter = array();
        if (!empty($field)) {
            $fieldFilter = $this->execute($field);
        }

        return $fieldFilter;
    }

    public function fieldDeleteChild($key)
    {
        $trim = $this->camelToSnake(preg_replace($this->regex, '', $key));
        $field = $this->request->get('field-del-' . $trim);
        $fieldFilter = array();
        if (!empty($field)) {
            $fieldFilter = $this->execute($field);
        }

        return $fieldFilter;
    }
}
