<?php

class Machine
{
    private $_memory;
    private $_registers;
    private $_stack;

    public function load($file)
    {
        $handle = fopen($file, 'r');
        $this->_memory = array();
        while (! feof($handle))
        {
            $low = fread($handle, 1);
            $high = fread($handle, 1);
            $byte = ((ord($high) << 8) | ord($low));
            $this->_memory[] = $byte;
        }
    }

    private function _read($addr)
    {
        if (0 <= $addr && $addr <= 32767)
        {
            return $addr;
        }
        else if (32768 <= $addr && $addr <= 32775)
        {
            $r = $addr - 32768;
            return $this->_registers[$r];
        }
        else
        {
            die("reading invalid address: $addr");
        }
    }

    private function _write($addr, $value)
    {
        if (0 <= $addr && $addr <= 32767)
        {
            $this->_memory[$addr] = $value;
        }
        else if (32768 <= $addr && $addr <= 32775)
        {
            $r = $addr - 32768;
            $this->_registers[$r] = $value;
        }
        else
        {
            die("writing invalid address: $addr");
        }
    }

    public function run()
    {
        $i = 0;
        $in = fopen('php://stdin', 'r');
        while (true)
        {
            $op = $this->_memory[$i];
            switch ($op)
            {
                case 0:
                    // halt
                    exit;
                case 1:
                    // set
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $this->_write($a, $b);
                    $i += 3;
                    break;
                case 2:
                    // push
                    $a = $this->_read($this->_memory[$i + 1]);
                    $this->_stack[] = $a;
                    $i += 2;
                    break;
                case 3:
                    // pop
                    $a = $this->_memory[$i + 1];
                    if (empty($this->_stack))
                    {
                        die('attempted popping an empty stack');
                    }
                    $ret = array_pop($this->_stack);
                    $this->_write($a, $ret);
                    $i += 2;
                    break;
                case 4:
                    // eq
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    if ($b == $c)
                    {
                        $this->_write($a, 1);
                    }
                    else
                    {
                        $this->_write($a, 0);
                    }
                    $i += 4;
                    break;
                case 5:
                    // gt
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    if ($b > $c)
                    {
                        $this->_write($a, 1);
                    }
                    else
                    {
                        $this->_write($a, 0);
                    }
                    $i += 4;
                    break;
                case 6:
                    // jmp
                    $i = $this->_read($this->_memory[$i + 1]);
                    break;
                case 7:
                    // jt
                    $a = $this->_read($this->_memory[$i + 1]);
                    $b = $this->_read($this->_memory[$i + 2]);
                    if ($a != 0)
                    {
                        $i = $b;
                    }
                    else
                    {
                        $i += 3;
                    }
                    break;
                case 8:
                    // jf
                    $a = $this->_read($this->_memory[$i + 1]);
                    $b = $this->_read($this->_memory[$i + 2]);
                    if ($a == 0)
                    {
                        $i = $b;
                    }
                    else
                    {
                        $i += 3;
                    }
                    break;
                case 9:
                    // add
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    $this->_write($a, ($b + $c) % 32768);
                    $i += 4;
                    break;
                case 10:
                    // mult
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    $this->_write($a, ($b * $c) % 32768);
                    $i += 4;
                    break;
                case 11:
                    // mod
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    $this->_write($a, $b % $c);
                    $i += 4;
                    break;
                case 12:
                    // and
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    $this->_write($a, $b & $c);
                    $i += 4;
                    break;
                case 13:
                    // or
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $c = $this->_read($this->_memory[$i + 3]);
                    $this->_write($a, $b | $c);
                    $i += 4;
                    break;
                case 14:
                    // not
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $b = ~$b;
                    $b &= 32767;
                    $this->_write($a, $b);
                    $i += 3;
                    break;
                case 15:
                    // rmem
                    $a = $this->_memory[$i + 1];
                    $b = $this->_read($this->_memory[$i + 2]);
                    $b = $this->_memory[$b];
                    $this->_write($a, $b);
                    $i += 3;
                    break;
                case 16:
                    // wmem
                    $a = $this->_read($this->_memory[$i + 1]);
                    $b = $this->_read($this->_memory[$i + 2]);
                    $this->_write($a, $b);
                    $i += 3;
                    break;
                case 17:
                    // call
                    $this->_stack[] = ($i + 2);
                    $i = $this->_read($this->_memory[$i + 1]);
                    break;
                case 18:
                    // ret
                    if (empty($this->_stack))
                    {
                        exit;
                    }
                    $ret = array_pop($this->_stack);
                    $i = $ret;
                    break;
                case 19:
                    // out
                    $char = chr($this->_read($this->_memory[$i + 1]));
                    echo $char;
                    $i += 2;
                    break;
                case 20:
                    // in
                    $a = $this->_memory[$i + 1];
                    $c = ord(fgetc($in));
                    // windows sends \r\n upon enter, ignore the 13
                    if ($c == 13)
                    {
                        // get the ascii 10
                        $c = ord(fgetc($in));
                    }
                    $this->_write($a, $c);
                    $i += 2;
                    break;
                case 21:
                    // noop
                    $i++;
                    break;
                default:
                    die("unknown op: $op");
                    break;
            }
        }
    }
}

$m = new Machine;
$m->load('challenge.bin');
$m->run();
