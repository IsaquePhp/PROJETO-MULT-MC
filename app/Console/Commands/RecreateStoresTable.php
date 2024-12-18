<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\User;
use App\Models\Store;

class RecreateStoresTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stores:recreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recria a tabela stores com a estrutura correta';

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
        $this->info('Iniciando recriação da tabela stores...');

        // Desabilitar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Dropar tabela existente
        Schema::dropIfExists('stores');

        // Criar nova tabela
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_matrix')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Reabilitar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Criar loja matriz para o usuário admin
        $user = User::where('email', 'admin@admin.com')->first();
        if ($user && $user->company_id) {
            Store::create([
                'company_id' => $user->company_id,
                'name' => 'Loja Matriz',
                'document' => '12.345.678/0001-90',
                'phone' => '(11) 99999-9999',
                'email' => 'matriz@example.com',
                'address' => 'Rua Principal, 123',
                'is_matrix' => true
            ]);
            $this->info('Loja matriz criada com sucesso!');
        }

        $this->info('Tabela stores recriada com sucesso!');
        return 0;
    }
}
