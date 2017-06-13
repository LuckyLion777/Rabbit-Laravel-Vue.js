<?php

namespace App\Models\Subscription;

use ArrayAccess;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Plan implements Arrayable, ArrayAccess
{
    private $id;
    private $level;
    private $amount;
    private $currency;
    private $name;
    private $statementDescriptor;
    private $setupPrice;
    private $maxBreeders;
    private $maxArchivedBreeders;

    protected function __construct($id, $level, $amount, $currency, $name, $statementDescriptor, $setupPrice,
                                   $maxBreeders, $maxArchivedBreeders)
    {
        $this->id = $id;
        $this->level = $level;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->name = $name;
        $this->statementDescriptor = $statementDescriptor;
        $this->setupPrice = (int) $setupPrice;
        $this->maxBreeders = isset($maxBreeders) ? (int) $maxBreeders : null;
        $this->maxArchivedBreeders = isset($maxArchivedBreeders) ? (int) $maxArchivedBreeders : null;
    }

    /**
     * @return static[]|Collection
     */
    public static function all()
    {
        return \Cache::rememberForever('stripe-plans', function() {
            $client = new Client();
            $request = $client->get('https://api.stripe.com/v1/plans', [
                'auth' => [config('services.stripe.secret'), '']
            ]);
            return collect(json_decode($request->getBody()->getContents())->data)->map(function($plan) {
                return new static($plan->id, $plan->metadata->level, $plan->amount, $plan->currency,
                                    $plan->name, $plan->statement_descriptor, @$plan->metadata->setup_price,
                                    @$plan->metadata->limit_breeder, @$plan->metadata->limit_breeder_archive);
            });
        });
    }

    /**
     * @param string $level
     * @return static[]|Collection
     */
    public static function ofLevel($level)
    {
        return static::all()->filter(function(Plan $plan) use($level) {
            return $plan->getLevel() === $level;
        });
    }

    /**
     * @return string[]
     */
    public static function basic()
    {
        return static::ofLevel('basic')->pluck('id')->toArray();
    }

    /**
     * @return string[]
     */
    public static function premium()
    {
        return static::ofLevel('premium')->pluck('id')->toArray();
    }

    /**
     * @param string $id
     * @return static
     */
    public static function find($id)
    {
        return static::all()->first(function($_, $plan) use ($id) {
            return $plan->id === $id;
        });
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatementDescriptor()
    {
        return $this->statementDescriptor;
    }

    public function getMaxBreeders()
    {
        return $this->maxBreeders;
    }

    public function getMaxArchivedBreeders()
    {
        return $this->maxArchivedBreeders;
    }

    public function getSetupPrice()
    {
        return $this->setupPrice;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'level' => $this->getLevel(),
            'amount' => $this->getAmount(),
            'setup_price' => $this->getSetupPrice(),
            'currency' => $this->getCurrency(),
            'name' => $this->getName(),
            'statement_descriptor' => $this->getStatementDescriptor(),
        ];
    }

    public function offsetExists($offset)
    {
        return method_exists($this, 'get' . ucfirst($offset));
    }

    public function offsetGet($offset)
    {
        return $this->{'get' . ucfirst($offset)}();
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadFunctionCallException();
    }

    public function offsetUnset($offset)
    {
        throw new \BadFunctionCallException();
    }
}
