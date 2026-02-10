<?php

namespace App\Console\Commands;

use Crypt;
use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;

class DecryptAisData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:decrypt-ais-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = 'base64:95nycaG+NerNjDbIx5a3CBbN0/pfk12bBID0blVpL0k=';

        $encryptedPayload = 'eyJpdiI6IjEyWFZsZUNVR3dzazc5d0ZEblJoNkE9PSIsInZhbHVlIjoiU1g2WkNWcS9wZzU0Z0dER3E5NWhTdz09IiwibWFjIjoiMzUzZDRmYTA2ZDFlMzdmZWQ2YWU0ZWZkOTBiMTBiZjI2ZGIyZGUwN2NmYjFmZTVlYjc3ZDcwNzQxOWQ0YjNjZSJ9';

        try {
            config()->set('app.key', $token);
//            $this->info(sprintf('APP_KEY: %s', config()->get('app.key')));
//            $en = Crypt::encrypt('test@mail.ru');
//            $this->info(sprintf('Encrypt str from TG: %s', $encryptedPayload));
//            $this->info(sprintf('Encrypt test@mail.ru: %s', $en));
//            $this->info(sprintf('Decrypt str: %s', Crypt::decrypt($en)));
            dump(Crypt::decryptString($encryptedPayload));
        } catch (DecryptException $e) {
            // Логируем детали (без раскрытия данных)

            \Log::warning('Ошибка расшифровки email', [
                'error' => $e->getMessage(),
                'payload_length' => strlen($encryptedPayload)
            ]);
            throw new \Exception('Неверные данные или повреждённая подпись', 0, $e);
        } catch (\Throwable $e) {
            \Log::error('Критическая ошибка расшифровки', ['exception' => $e]);
            throw new \Exception('Ошибка обработки шифрования', 0, $e);
        }
    }
}
