<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testmemory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:memory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test runner to check for memory leaks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function processTree($id) {
        $memory = memory_get_usage();

        echo "\n$memory : Before\n";


        $tree = \App\Tree::find($id);

        $memory = memory_get_usage();

        echo "$memory : After\n";
        echo "==================\n";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $iterations = 100;

        for($i=0;$i<$iterations;$i++) {
            $id = rand(0, 250000);

            $memory = memory_get_usage();
            echo "\n$memory : Before\n";

            $tree = \App\Tree::find($id);
            
            $memory = memory_get_usage();
            echo "$memory : After\n";
            echo "==================\n";
            
        }



    }
}
