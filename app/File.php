<?php

class File
{
    protected $data = null;
    protected $filePath = null;
    protected $filename = null;
    protected $distruptions = [];

    public function __construct($filePath) {
        $this->filePath = $filePath;
        $this->filename = basename($filePath);
        $this->data = json_decode(file_get_contents($filePath));
        if(is_null($this->data) || is_null($this->data->disruptions)) {
            return;
        }
        foreach($this->data->disruptions as $dataDistruption) {
            foreach($this->data->lines as $line) {
                foreach($line->impactedObjects as $object) {
                    if($object->type != "line") {
                      continue;
                    }
                    if(in_array($dataDistruption->id, $object->disruptionIds)) {
                        $dataDistruption->lines[] = $line->mode." ".$line->name;
                    }
                }
            }
            $impact = new Impact($dataDistruption, $this);
            if($impact->isToExclude()) {
                continue;
            }
            $this->distruptions[$dataDistruption->id] = $impact;
        }
    }

    public function getDate() {

        return new DateTime(preg_replace("/^([0-9]{8})/", '\1T', preg_replace("/_.*.json/", "", $this->filename)));
    }

    public function getLastUpdatedDate() {
        $dateUpdated = new DateTime($this->data->lastUpdatedDate, new DateTimeZone("UTC"));
        $dateUpdated->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        return $dateUpdated;
    }

    public function getDistruptions() {

        return $this->distruptions;
    }

    public function getFilePath() {

        return $this->filePath;
    }


}
