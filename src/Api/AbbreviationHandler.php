<?php

namespace Hcr\Api;

use \Hcr\Api\DbHandler;

/**
 * Handle request for abbreviation
 */
class AbbreviationHandler extends DbHandler
{
    const CREATE_ABBREVIATION = 'save';

    const FETCH_ABBREVIATIONS = 'fetch';

    const GET_ABBREVIATION = 'get';

    const UPDATE_ABBREVIATION_MEANING = 'update_meaning';

    const APPEND_ABBREVIATION_MEANING = 'append_meaning';

    const DELETE_ABBREVIATION = 'delete';

    /**
     * Constructor
     * @param string|null $dbPath
     */
    public function __construct($dbPath = null)
    {
        if (is_null($dbPath)) {
            $dbPath = getcwd() . '/abbr';
        }
        parent::__construct($dbPath);
    }
    /**
     * Accept request from client
     * @param  string $type
     * @param  mixed $data
     * @return mixed
     */
    public function acceptRequest($type = '', $data = null)
    {
        switch ($type) {
            case self::CREATE_ABBREVIATION:
                return $this->save(
                    $data
                );
            case self::FETCH_ABBREVIATIONS:
                return $this->fetch();
            case self::GET_ABBREVIATION:
                return $this->get(
                    $data
                )->getData();
            case self::UPDATE_ABBREVIATION_MEANING:
                return $this->update(
                    $data
                );
            case self::APPEND_ABBREVIATION_MEANING:
                return $this->update(
                    $data,
                    self::APPEND_ABBREVIATION_MEANING
                );
            case self::DELETE_ABBREVIATION:
                return $this->delete(
                    $data
                );
            default:
                throw new \Exception("Unsupported Request", 1);

        }
    }
    /**
     * Save new abbreviation
     * @param  array $newData
     * @return array
     */
    public function save($newData)
    {
        $db = $this->fileBase->get($newData['abbreviation']);

        $db->abbreviation = $newData['abbreviation'];

        $db->meaning = $newData['meaning'];

        $db->status = 'active';

        $status = $db->save();

        if ($status) {
            // return array(
            //     'abbreviation' => $newData,
            //     'message'      => 'Success',
            // );
            return $newData;
        }
        return array(
            'message' => 'Failed',
        );
    }
    public function update($newData, $operation = self::UPDATE_ABBREVIATION_MEANING)
    {
        // case
        // 1. meaning update (replace old value)
        // done when using usual put method api
        // 2. meaning addition ([meaning1,newMeaning])
        // when create same abbreviation name

        $oldData = $this->get($newData['abbreviation']);

        // filter
        
        $newData['meaning'] = array_values(array_filter($newData['meaning'], function ($v) {
            return !empty($v);
        }));

        $newData['meaning'] = $this->filterSingularArray($newData['meaning']);

        if (!is_null($oldData) && !empty($oldData)) {
            switch ($operation) {
                case self::UPDATE_ABBREVIATION_MEANING:
                    foreach ($newData as $key => $value) {
                        $oldData->$key = $value;
                    }

                    $status = $oldData->save();

                    if ($status) {
                        return $newData;
                    }
                    return array(
                        'message' => "Failed to update",
                    );
                case self::APPEND_ABBREVIATION_MEANING:
                    $isUpdated = false;
                    if (!is_array($oldData->meaning)) {
                        if ($oldData->meaning != $newData['meaning']) {
                            $oldData->meaning = array(
                                $oldData->meaning,
                                $newData['meaning'],
                            );
                            $isUpdated = true;
                        }
                    } else {
                        $newMeaning = $oldData->meaning;
                        if (!in_array($newData['meaning'], $newMeaning)) {
                            array_push($newMeaning, $newData['meaning']);
                            $oldData->meaning = $newMeaning;
                            $isUpdated        = true;
                        }
                    }
                    if ($isUpdated) {
                        $status = $oldData->save();
                        if ($status) {
                            $newData = $oldData->getData();
                            return array(
                                'message' => 'Success',
                                'data'    => $newData,
                            );
                        }
                        return array(
                            'message' => "Failed to update",
                        );
                    } else {
                        return array(
                            'message'=>'Nothing updated'
                        );
                    }

                    // no break
                default:
                    throw new \Exception("Unsupported update operation", 1);

            }
        }

        return array(
            'message' => "abbreviation not found",
        );
    }
    /**
     * Delete abbreviation
     * @param  array $abbreviation
     * @return array
     */
    public function delete($abbreviation)
    {
        $item = $this->get($abbreviation);

        $oldData = array(
            'abbreviation'=>$item->abbreviation,
            'meaning'=>$item->meaning,
            'status'=>$item->status
        );

        if (!is_null($item)&&!empty($item)) {
            $status = $item->delete();
            if ($status) {
                return $oldData;
            }
            return array(
                'message'=>'Unable to delete'
            );
        }
        return array(
            'message'=>"abbreviation not found"
        );
    }
    /**
     * Fetch abbreviations
     * @return array
     */
    public function fetch()
    {
        $data = $this->fileBase->query()->results(true);

        return array_values(array_filter($data, function ($v) {
            //file_put_contents('aduh.txt', print_r($v, true),FILE_APPEND | LOCK_EX);
            return !empty($v) && $v['status'] == 'active';
        }));
    }
    /**
     * Get abbreviation from abbreviation name
     * @param  string $abbreviation
     * @return Document
     */
    public function get($abbreviation)
    {
        return $this->fileBase->get($abbreviation);
    }
    /**
     * Check for sameness
     * @param  Document $oldData
     * @param  array $newData
     * @return boolean
     */
    private function isSame($oldData, $newData)
    {
        return (
            $oldData->abbreviation == $newData['abbreviation']
            &&
            $oldData->meaning == $newData['meaning']
        );
    }
    /**
     * Replace old value with new value
     * Based on key
     * @param  mixed $item
     * @param  mixed $key
     * @param  mixed $newValue
     * @return mixed
     */
    private function replaceValue($item, $key, $newValue)
    {
        if (is_object($item)) {
            $item->$key = $newValue;
        }
        return $item;
    }
    /**
     * Filter singular array
     * If singular array
     * return the value
     * @param  array  $arr
     * @return mixed
     */
    private function filterSingularArray(array $arr)
    {
        if (count($arr) == 1) {
            return current($arr);
        }
        return $arr;
    }
}
