<?php

namespace App\Console\Commands;

use App\Events\DayEndedEvent;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTimeEntriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-time-entries-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all users have added their time entries for today and notifies those who have not';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = new \DateTime();
        $this->info("Iniciando verificación de entradas de tiempo para: " . $today->format('d-m-Y H:i:s'));

        // Obtener todos los usuarios que deberían registrar tiempo.
        // Puedes filtrar aquí si no todos los usuarios deben hacerlo,
        // por ejemplo, usuarios activos: User::where('is_active', true)->get();
        $users = User::all();

        if ($users->isEmpty()) {
            $this->info("No se encontraron usuarios para verificar.");
            return Command::SUCCESS;
        }

        $usersWithoutEntriesCount = 0;

        foreach ($users as $user) {
            // Asumimos que tu modelo TimeEntry tiene un campo 'user_id'
            // y un campo de fecha llamado 'entry_date'.
            // Si usas 'created_at' para la fecha de la entrada Y la entrada
            // siempre se crea el día al que corresponde, puedes usar whereDate('created_at', $today).
            // ¡AJUSTA 'entry_date' AL NOMBRE REAL DE TU COLUMNA DE FECHA EN TimeEntry!
            $hasEntriesToday = TimeEntry::where('user_id', $user->id)
                ->whereDate('date', $today) // O ->whereDate('created_at', $today)
                ->get();

            if (count($hasEntriesToday) === 0) {
                $this->warn("Usuario {$user->name} (ID: {$user->id}) no tiene entradas de tiempo para hoy.");

                // Disparar el evento para este usuario
                // El listener asociado a DayEndedEvent se encargará de la notificación.
                try {
                    event(new DayEndedEvent($user->id));
                    Log::info("DayEndedEvent despachado para el usuario {$user->id}");
                    $usersWithoutEntriesCount++;
                } catch (\Exception $e) {
                    $this->error("Error: " . $e->getMessage());
                    Log::error("Error");
                }
            } else {
                $this->line("Usuario {$user->name} (ID: {$user->id}) ya tiene entradas de tiempo.");
            }
        }

        if ($usersWithoutEntriesCount > 0) {
            $this->info("Verificación completada. {$usersWithoutEntriesCount} usuario(s) serán notificado(s) (si el listener está configurado).");
        } else {
            $this->info("Verificación completada. Todos los usuarios elegibles tienen sus entradas de tiempo para hoy.");
        }

        return Command::SUCCESS;
    }
}
