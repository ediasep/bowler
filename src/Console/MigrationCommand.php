<?php

namespace Ediasep\Bowler\Console;

use Illuminate\Console\Command;
use Ediasep\Bowler\Helper\BowlerHelper;

class MigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bowler:migration {database} {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will execute bowler and convert your table into migration';


    /**
     * Load Bowler helper class object
     *
     * @var string
     **/
    protected $helper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BowlerHelper $helper)
    {
        parent::__construct();
        $this->helper = $helper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $args   = $this->arguments();

        $this->info("Retrieving table field...");
        $fields = $this->helper->getTableFields($args[ 'database' ], $args[ 'table' ]);

        $this->info("Creating Migration...");
        $filename = $this->helper->createMigration($fields, $args[ 'table' ]);
        
        $this->info("Migration created : ".$filename);
    }
}