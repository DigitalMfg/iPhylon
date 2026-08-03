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

function getPreviousShift($conn,$date,$shift)
{

    /*
    |--------------------------------------------------------------------------
    | Shift 1
    |--------------------------------------------------------------------------
    | Ambil Shift 3 terakhir yang tersedia
    | (aman jika ada libur Sabtu/Minggu)
    */

    if($shift == 1)
    {
        $sql = mysqli_query($conn,"
            SELECT tanggal
            FROM tbl_shift_wip
            WHERE shift = 3
            AND tanggal < '$date'
            ORDER BY tanggal DESC
            LIMIT 1
        ");

        if(mysqli_num_rows($sql))
        {
            $row = mysqli_fetch_assoc($sql);

            return [
                'date'  => $row['tanggal'],
                'shift' => 3
            ];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Shift 2
    |--------------------------------------------------------------------------
    | Selalu mengambil Shift 1 hari yang sama
    */

    if($shift == 2)
    {
        return [
            'date'  => $date,
            'shift' => 1
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Shift 3
    |--------------------------------------------------------------------------
    | Ambil Shift 2 terakhir yang tersedia
    | (aman jika melewati libur)
    */

    if($shift == 3)
    {
        $sql = mysqli_query($conn,"
            SELECT tanggal
            FROM tbl_shift_wip
            WHERE shift = 2
            AND tanggal <= '$date'
            ORDER BY tanggal DESC
            LIMIT 1
        ");

        if(mysqli_num_rows($sql))
        {
            $row = mysqli_fetch_assoc($sql);

            return [
                'date'  => $row['tanggal'],
                'shift' => 2
            ];
        }
    }


    return [
        'date'  => $date,
        'shift' => 1
    ];
}