<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DbLog extends Model
{
    //
    protected $connection = '';

    protected $table = 'd2d_ord_detail';

    public function set_connection($val)
    {
        $this->connection = $val;
    }

    // public function select_all_db2_users()
    // {
    //     $sql = "
    //     SELECT *
    //     FROM $this->table
    //     WHERE scNumber = '2' LIMIT 1;
    //     ";
    //     $results = DB::connection($this->connection)
    //         ->select(DB::raw($sql));
    //     return $results;
    // }

    // public function select_all_db_users()
    // {
    //     $sql = "
    //         SELECT *
    //         FROM $this->table
    //         LIMIT 0,1;
    //     ";
    //     $results = DB::connection($this->connection)
    //         ->select(DB::raw($sql));
    //     return $results;
    // }

    // public function close_connection()
    // {
    //     DB::disconnect('mysql2');
    // }
}
