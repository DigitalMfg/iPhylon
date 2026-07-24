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

function getPreviousShift($date, $shift)
{
    if($shift == 1)
    {
        // Shift 1 mengambil Last WIP dari Shift 3 di hari yang sama
        return [
            'date'  => $date,
            'shift' => 3
        ];
    }

    if($shift == 2)
    {
        // Shift 2 mengambil Last WIP dari Shift 1 di hari yang sama
        return [
            'date'  => $date,
            'shift' => 1
        ];
    }

    // Shift 3 mengambil Last WIP dari Shift 2 hari sebelumnya
    return [
        'date'  => date('Y-m-d', strtotime($date.' -1 day')),
        'shift' => 2
    ];
}