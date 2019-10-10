<?php

/*
已知：
1.三个转子的字母顺序已知：如
转子a:
1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26
1,19,10,14,26,20,8,16,7,22,4,11,5,17,9,12,23,18,2,25,6,24,13,21,3,15
转子b:
1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26
1,6,4,15,3,14,12,23,5,16,2,22,19,11,18,25,24,13,7,10,8,21,9,26,17,20
转子c:
1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26
8,18,26,17,20,22,10,3,13,11,4,23,5,24,9,12,25,16,19,6,15,21,2,7,1,14
2.三个转子的初始旋转位置不确定,如：图一中，最左的转子24对应字母A,初始状态也可以是其他位置对应字母A,中转子和快转子同理。

Input
RAXLZSDKQAGECXWGVK

Output
PAGODA***********
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Templ
{
    protected $seq_1 = [1,2,3];
    protected $seq_2 = [4,5,6];

    public function __construct(int $offset = 0)
    {
        for ($j = 0; $j < $offset; $j++) {
            $len = count($this->seq_1);
            $end = $this->seq_1[$len - 1];
            for ($i = $len - 1; $i > 0; $i--) {
                $this->seq_1[$i] = $this->seq_1[$i - 1];
            }
            $this->seq_1[0] = $end;
            $end = $this->seq_2[$len - 1];
            for ($i = $len - 1; $i > 0; $i--) {
                $this->seq_2[$i] = $this->seq_2[$i - 1];
            }
            $this->seq_2[0] = $end;
        }
    }

    public function calc(int $offset) {
        $num = $this->seq_1[$offset];
        $idx = $this->indexOf($this->seq_2, $num);
        return $idx;
    }

    public function dcalc(int $offset) {
        $num = $this->seq_2[$offset];
        $idx = $this->indexOf($this->seq_1, $num);
        return $idx;
    }

    public function rota()
    {
            $len = count($this->seq_1);
            $end = $this->seq_1[$len - 1];
            for ($i = $len - 1; $i > 0; $i--) {
                $this->seq_1[$i] = $this->seq_1[$i - 1];
            }
            $this->seq_1[0] = $end;
            $end = $this->seq_2[$len - 1];
            for ($i = $len - 1; $i > 0; $i--) {
                $this->seq_2[$i] = $this->seq_2[$i - 1];
            }
            $this->seq_2[0] = $end;
    }

    private function indexOf(array $arr, $data): int
    {
        foreach ($arr as $i => $v) {
            if ($data == $v) {
                return $i;
            }
        }
        return -1;
    }
}

class A extends Templ
{
    protected $seq_1 = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26];
    protected $seq_2 = [1,19,10,14,26,20,8,16,7,22,4,11,5,17,9,12,23,18,2,25,6,24,13,21,3,15];
}

class B extends Templ
{
    protected $seq_1 = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26];
    protected $seq_2 = [1,6,4,15,3,14,12,23,5,16,2,22,19,11,18,25,24,13,7,10,8,21,9,26,17,20];
}

class C extends Templ
{
    protected $seq_1 = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26];
    protected $seq_2 = [8,18,26,17,20,22,10,3,13,11,4,23,5,24,9,12,25,16,19,6,15,21,2,7,1,14];
}

class Spider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '第二届编程大赛报名密码破解程序开发的存档';

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
     * @return mixed
     */
    public function handle()
    {
        $in = 'PAGODA';
        $out = 'RAXLZSDKQAGECXWGVK';
        for ($l = 0; $l < 26; $l++) {
            $a = new A($l);
            for ($m = 0; $m < 26; $m++) {
                $b = new B($m);
                for ($n = 0; $n < 26; $n++) {
                    $c = new C($n);
                    $calc_out = '';
                    $info = false;
                    for ($k = 0; $k < mb_strlen($in); $k++) {
                        $initOffset = ord($in[$k]) - ord('A');
                        $result = $c->calc($b->calc($a->calc($initOffset)));
                        $res = chr($result + ord('A'));
                        $calc_out .= $res;
                        $c->rota();
                    }
                    if (mb_stripos($out, $calc_out) === 0) {
                        $c = new C($n);
                        $src = '';
                        for ($j = 0; $j < mb_strlen($out); $j++) {
                            $src .= chr($a->dcalc($b->dcalc($c->dcalc(ord($out[$j]) - ord('A')))) + ord('A'));
                            $c->rota();
                        }
                        $this->info($src);
                    }
                }
            }
        }
    }
}

