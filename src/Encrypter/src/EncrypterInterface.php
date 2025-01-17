<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Encrypter;

use Spiral\Encrypter\Exception\DecryptException;
use Spiral\Encrypter\Exception\EncrypterException;
use Spiral\Encrypter\Exception\EncryptException;

/**
 * Immutable class responsible for encryption services.
 */
interface EncrypterInterface
{
    /**
     * Create and encrypter instance with new key.
     *
     *
     * @throws EncrypterException
     */
    public function withKey(string $key): EncrypterInterface;

    /**
     * Encryption ket value. Returns in a format of ANSI string.
     */
    public function getKey(): string;

    /**
     * Encrypt data into encrypter specific payload string. Can be decrypted only using decrypt()
     * method.
     *
     * @see decrypt()
     *
     * @param mixed $data
     *
     * @throws EncryptException
     * @throws EncrypterException
     */
    public function encrypt($data): string;

    /**
     * Decrypt payload string. Payload should be generated by same encrypter using encrypt() method.
     *
     * @see encrypt()
     *
     *
     * @return mixed
     * @throws DecryptException
     * @throws EncrypterException
     */
    public function decrypt(string $payload);
}
