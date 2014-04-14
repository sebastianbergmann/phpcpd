<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcpd
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 2.0.x
 */

namespace SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage;

/**
 * The SQLite HashStorageAdapter stores all hashes within temporary SQLite
 * Database. It has a small memory footprint when used, but uses the filesystem and is
 * slower than the Memory Adapter.
 * 
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @author    Matthias Glaub <magl@magl.net>
 * @copyright 2009-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release Release 2.0.x
 */
class SQLite implements HashStorageInterface
{

    private $dbFile;
    private $db;
    private $bufferSize = 10000;
    private $hashBuffer = array();
    private $insertStatement;
    private static $CREATE_SQL = 'CREATE TABLE IF NOT EXISTS hashes (hash TEXT PRIMARY KEY, value TEXT)';
    private static $INSERT_SQL = 'INSERT OR REPLACE INTO hashes (hash, value) VALUES (:hash, :value)';

    public function __construct(array $options = null)
    {
        if (!extension_loaded('pdo_sqlite')) {
            throw new \Exception('php module "pdo_sqlite" is not loaded');
        }
        
        if($options && isset($options['buffer_size'])){
            $this->bufferSize = (int) $options['buffer_size'];
        }
        
        // prepare sqlite database
        $this->dbFile = tempnam(sys_get_temp_dir(), 'phpcpd.sqlite.');
        $this->db = new \PDO('sqlite:' . $this->dbFile);
        $this->db->exec(self::$CREATE_SQL);
        $this->insertStatement = $this->db->prepare(self::$INSERT_SQL);

    }

    public function __destruct()
    {
        unset($this->insertStatement);
        unset($this->db);
        unlink($this->dbFile);
    }

    public function get($hash)
    {
        // check our buffer first
        if (isset($this->hashBuffer[$hash])) {
            return $this->hashBuffer[$hash];
        }

        $hash = md5($hash);
        $result = $this->db->query('SELECT value FROM hashes WHERE hash=' . $this->db->quote($hash));
        $row = $result->fetch();
        return unserialize($row['value']);
    }

    public function has($hash)
    {
        // check our buffer first
        if (isset($this->hashBuffer[$hash])) {
            return true;
        }

        $hash = md5($hash);
        $ret = $this->db->query('SELECT 1 FROM hashes WHERE hash=' . $this->db->quote($hash));
        $value = $ret->fetch();
        return $value == true;
    }

    public function set($hash, $value)
    {
        $this->hashBuffer[$hash] = $value;
        $this->checkFlush();
    }

    private function checkFlush()
    {
        if (count($this->hashBuffer) >= $this->bufferSize) {
            $this->flushBuffer();
        }
    }

    private function flushBuffer()
    {
        $this->db->beginTransaction();

        foreach ($this->hashBuffer as $hash => $value) {
			$hash_md5 = md5($hash);
			$value_serialized = serialize($value);
            $this->insertStatement->bindParam(':hash', $hash_md5);
            $this->insertStatement->bindParam(':value', $value_serialized);
            $this->insertStatement->execute();
        }
        $this->hashBuffer = array();

        $this->db->commit();
    }

	public function getDBFilename(){
		return $this->dbFile;
	}
}
