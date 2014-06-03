<?php

/*
 * Looking Glass - An easy to deploy Looking Glass
 * Copyright (C) 2014 Guillaume Mazoyer <gmazoyer@gravitons.in>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */

require_once 'authentication.php';

class Telnet extends Authentication {
  private $port;

  public function __construct($config) {
    parent::__construct($config);

    $this->port = isset($this->config['port']) ? $this->config['port'] : 23;
  }

  protected function check_config() {
    if (!isset($this->config['user']) || !isset($this->config['pass'])) {
      throw new Exception('User and password required for telnet.');
    }
  }

  public function connect() {
    $this->connection = fsockopen($this->config['host'], $this->port);
    if (!$this->connection) {
      throw new Exception('Cannot connect to router.');
    }

    fputs($this->connection, $this->config['user']."\r");
    fputs($this->connection, $this->config['pass']."\r");
  }

  public function send_command($command) {
    if ($this->connection == null) {
      $this->connect();
    }

    $data = '';
    while(!feof(!$this->connection)) {
      $data .= fread($this->connection, 4096);
    }

    return $data;
  }

  public function disconnect() {
    fclose($this->connection);
    $this->connection = null;
  }
}

// End of telnet.php