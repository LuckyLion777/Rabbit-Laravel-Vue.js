<?php

namespace App\Jobs;

use App\Models\Ledger\Category;
use App\Repositories\LedgerCategoryRepository;
use App\Repositories\LedgerEntryRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class CreateLedgerEntryJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $date;
    /**
     * @var int
     */
    public $category_id;
    /**
     * @var int
     */
    public $associated_id;
    /**
     * @var string
     */
    public $associated_type = null;
    /**
     * @var string
     */
    public $category_name;
    /**
     * @var bool
     */
    public $debit;
    /**
     * @var number
     */
    public $amount;
    /**
     * @var string
     */
    public $description;
    /**
     * @var int
     */
    public $user_id;

    /**
     * Create a new job instance.
     * @param $name
     * @param $date
     * @param $category_id
     * @param $category_name
     * @param $debit
     * @param $amount
     * @param $description
     */
    public function __construct($name, $date, $category_id, $debit, $amount, $description, $category_name = null, $associated_id = null)
    {
        $this->name = $name;
        $this->date = $date;
        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->debit = $debit;
        $this->amount = $amount;
        $this->description = $description;
        $this->associated_id = $associated_id;

        $this->user_id = auth()->id();
    }

    protected function createCategory(LedgerCategoryRepository $categoriesRepository)
    {
        $categories = Category::where('name', $this->category_name)->where(function(Builder $builder) {
            $builder->where('user_id', auth()->id())->orWhere('special', '<>', '');
        });
        if ($category = $categories->first()) {
            $this->category_id = $category->id;
            return;
        }

        $createCategory = new CreateLedgerCategoryJob($this->category_name, null);
        $this->category_id = $createCategory->handle($categoriesRepository)->id;
    }

    protected function figureAssociation()
    {
        $category = Category::find($this->category_id);
        $this->associated_type = $category->special;
    }

    /**
     * Execute the job.
     *
     * @param LedgerEntryRepository $ledger
     * @param LedgerCategoryRepository $categories
     * @return mixed
     */
    public function handle(LedgerEntryRepository $ledger, LedgerCategoryRepository $categories)
    {
        if (!$this->category_id) {
            $this->createCategory($categories);
        }
        if ($this->associated_id) {
            $this->figureAssociation();
        }

        return $ledger->createFromJob($this);
    }
}
