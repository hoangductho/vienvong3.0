<?php
/**
 * PHPSeclib Bootstrap Class
 */

/**
 * --------------------------------------------------------------------
 * Read Directory To Get All PHP file
 * --------------------------------------------------------------------
 *
 * @param string $dir directory needed read
 * @param bool $saveFile get file of current directory or not
 *
 * @return void
 */
function read_path($dir, $saveFile = true) {
	$list = scandir($dir);
	$files = array();

	foreach ($list as $path) {
		$fullPath = $dir. DIRECTORY_SEPARATOR .$path;
		if(!in_array($path, array('.', '..')) && is_dir($fullPath)) {
			$newList = read_path($fullPath);

			foreach ($newList as $value) {
				$files[] = $value;
			}
		}elseif($saveFile) {
			$info = new SplFileInfo($fullPath);
			if($info->getExtension() == 'php') {
				$files[] = $fullPath;
			}
		}
	}

	return $files;
}

/**
 * --------------------------------------------------------------------
 * Require List File
 * --------------------------------------------------------------------
 *
 * @param array $list list path needed
 *
 * @return void
 */
function require_list($list) {
	foreach ($list as $value) {
		require_once($value);
	}
}

/**
 * --------------------------------------------------------------------
 * Including PHPSeclib
 * --------------------------------------------------------------------
 *
 */
$files = read_path(__DIR__, 0);

foreach ($files as $key => $value) {
	$files[$key] = str_replace(__DIR__ . '/', '', $value);
}

require_list($files);

/**
 * --------------------------------------------------------------------
 * Create PHPSeclib Library
 * --------------------------------------------------------------------
 *
 */
class Phpseclib {
	/**
	 * ==========================================
	 * RSA Key Init
	 * ==========================================
	 * Create RSA Key pack
	 *
	 * @return array $key with publickey, privatekey and publicHex key
	 */
	public function rsaKeyInit() {
		$rsa = new Crypt_RSA();
		// set format for public key
		$rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
		// create keys
		$create = $rsa->createKey();
		// get public key and private key
		$key['public'] = $create['publickey'];
		$key['private'] = $create['privatekey'];
		// set private key to crate publickey Hexa
		$rsa->loadKey($key['private']);
		$raw = $rsa->getPublicKey(CRYPT_RSA_PUBLIC_FORMAT_RAW);
		$key['publicHex'] = $raw['n']->toHex();
		// return rsa key
		return $key;
	}
	// ----------------------------------------------------------------
	/**
	 * ==========================================
	 * RSA Decrypt CryptoJS Encrypted Data
	 * ==========================================
	 *
	 * @param string $data hexa string
	 * @param array $key rsa key package
	 *
	 * @return decrypted data
	 */
	public function rsaDecryptCryptoJS($data, $key) {
		$rsa = new Crypt_RSA();
		// CryptoJS RSA Encrypted is string Hexa
		// Needed convert to String Base64
		$data = pack('H*', $data);
		// Setup private key for RSA method
        $rsa->loadKey($key['private']);
        // Setup mode padding to decrypt
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        // Decrypt data
        $decrypted = $rsa->decrypt($data);

        return $decrypted;
	}
	// ----------------------------------------------------------------
	/**
	 * ==========================================
	 * AES Encrypt
	 * ==========================================
	 *
	 * @param string $data data needed encrypt
	 * @param string $key AES key to encrypt/decrypt
	 * @param string $iv AES initilization vector to encrypt/decrypt
	 *
	 * @return string base64
	 */
	public function aesEncrypt($data, $key, $iv) {
		$aes = new Crypt_AES();
        $aes->setKey($key);
        $aes->setIV($iv);
        return base64_encode($aes->encrypt($data));
	}
	// ----------------------------------------------------------------
	/**
	 * ==========================================
	 * AES Encrypt For CryptoJS
	 * ==========================================
	 *
	 * @param string $data data needed encrypt
	 * @param string $key Hexa AES key to encrypt/decrypt
	 * @param string $iv  Hexa AES initilization vector to encrypt/decrypt
	 *
	 * @return string base64
	 */
	public function aesEncryptCryptoJS($data, $key, $iv) {
        return $this->aesEncrypt($data, pack('H*', $key), pack('H*', $iv));
	}
	// ----------------------------------------------------------------
	/**
	 * ==========================================
	 * AES Decrypt
	 * ==========================================
	 *
	 * @param string $data data base64 needed decrypt
	 * @param string $key AES key to encrypt/decrypt
	 * @param string $iv AES initilization vector to encrypt/decrypt
	 *
	 * @return string base64
	 */
	public function aesDecrypt($data, $key, $iv) {
		$aes = new Crypt_AES();
        $aes->setKey($key);
        $aes->setIV($iv);
        return $aes->decrypt(base64_decode($data));
	}
	// ----------------------------------------------------------------
	/**
	 * ==========================================
	 * AES Decrypt For CryptoJS
	 * ==========================================
	 *
	 * @param string $data data base64 needed decrypt
	 * @param string $key Hexa AES key to encrypt/decrypt
	 * @param string $iv  Hexa AES initilization vector to encrypt/decrypt
	 *
	 * @return string base64
	 */
	public function aesDecryptCryptoJS($data, $key, $iv) {
        return $this->aesDecrypt($data, pack('H*', $key), pack('H*', $iv));
	}
}