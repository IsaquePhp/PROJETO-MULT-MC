<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDefaultStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:create-default {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria uma loja matriz para o usuário especificado';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário com email {$email} não encontrado");
            return 1;
        }

        if (!$user->company_id) {
            $this->error("Usuário não possui uma empresa vinculada");
            return 1;
        }

        $store = \App\Models\Store::where('company_id', $user->company_id)
            ->where('is_matrix', true)
            ->first();

        if ($store) {
            $this->info("Loja matriz já existe para este usuário");
            return 0;
        }

        $store = \App\Models\Store::create([
            'company_id' => $user->company_id,
            'name' => 'Loja Matriz',
            'document' => '12.345.678/0001-90',
            'phone' => '(11) 99999-9999',
            'email' => 'matriz@example.com',
            'address' => 'Rua Principal, 123',
            'is_matrix' => true
        ]);

        $this->info("Loja matriz criada com sucesso!");
        return 0;
    }
}
