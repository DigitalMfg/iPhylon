<?php

function getCurrentShift($conn)
{
    $sql = mysqli_query($conn,"
        SELECT date, shift
        FROM tbl_master_time
        WHERE CURDATE() = date
        AND CURTIME() BETWEEN time_start AND time_end
        LIMIT 1
    ");

    if(mysqli_num_rows($sql))
    {
        return mysqli_fetch_assoc($sql);
    }

    return [
        'date'  => date('Y-m-d'),
        'shift' => 1
    ];
}


function getPreviousShift($conn, $date, $shift)
{
    /*
    |--------------------------------------------------------------------------
    | Tentukan shift sebelumnya
    |--------------------------------------------------------------------------
    |
    | Shift 1 -> Shift 3
    | Shift 2 -> Shift 1
    | Shift 3 -> Shift 2
    |
    */

    if($shift == 1)
    {
        $previousShift = 3;
    }
    elseif($shift == 2)
    {
        $previousShift = 1;
    }
    elseif($shift == 3)
    {
        $previousShift = 2;
    }
    else
    {
        $previousShift = 3;
    }


    /*
    |--------------------------------------------------------------------------
    | Cari WIP terakhir dari shift sebelumnya
    |--------------------------------------------------------------------------
    |
    | TIDAK memperhatikan tanggal.
    |
    | Yang dicari adalah record terakhir berdasarkan:
    | tanggal DESC
    | id DESC
    |
    */

    $sql = mysqli_query($conn,"
        SELECT tanggal, shift
        FROM tbl_shift_wip
        WHERE shift = '$previousShift'
        ORDER BY tanggal DESC, id DESC
        LIMIT 1
    ");


    /*
    |--------------------------------------------------------------------------
    | Jika ditemukan
    |--------------------------------------------------------------------------
    */

    if(mysqli_num_rows($sql))
    {
        $row = mysqli_fetch_assoc($sql);

        return [
            'date'  => $row['tanggal'],
            'shift' => $row['shift']
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Jika belum ada data shift sebelumnya
    |--------------------------------------------------------------------------
    */

    return [
        'date'  => $date,
        'shift' => $previousShift
    ];
}