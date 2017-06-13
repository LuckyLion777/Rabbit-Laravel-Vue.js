<?php

namespace App\Models\Transfer;

use App\Models\Traits\Transferable\Transferable;
use Illuminate\Contracts\Support\Arrayable;

class TransferResult implements Arrayable
{
    /**
     * @var Arrayable|Transferable
     */
    private $source;

    /**
     * @var Arrayable
     */
    private $target;

    /**
     * @var string
     */
    private $url;
    
    public function __construct($source, $target, $url)
    {
        $this->source = $source;
        $this->target = $target;
        $this->url = $url;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
    
    public function getUrl()
    {
        return $this->url;
    }

    public function toArray()
    {
        return [
            'source' => $this->source->toArray(),
            'target' => $this->target->toArray(),
            'url' => $this->url,
        ];
    }
}
