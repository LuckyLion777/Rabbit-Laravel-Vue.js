<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    public function object()
    {
        return $this->morphTo();
    }

    public function getObjectData()
    {
        $data = $this->object->toArray();
        $data['read'] = !!$this->read_at;
        $data['seen'] = !!$this->seen_at;
        $data['notification_id'] = $this->getKey();
        return $data;
    }
}
