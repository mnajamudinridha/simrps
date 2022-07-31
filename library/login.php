<?php
if (isset($_POST['login'])) {
    $username = clear($_POST['username']);
    $password = clear($_POST['password']);
    $query = mysqli_query($con, "SELECT * FROM user WHERE username = '$username' AND delete_at IS NULL");
    $jumlah = mysqli_num_rows($query);
    if ($jumlah < 1) {
        header('location:index.php?menu=login&pesan=error');
    } else {
        $var = mysqli_fetch_array($query);
        //cek password sama
        if (password_verify($password, $var['password'])) {
            //sukses
            $level = mysqli_query($con, "SELECT userlevel.level, level.icon as level_icon, level.nama as level_nama, level.kode as level_kode FROM userlevel 
                                         LEFT JOIN level ON userlevel.level = level.uuid
                                         WHERE userlevel.user='" . $var['uuid'] . "' ORDER BY level.id ASC");
            $listlevel = array();
            $levelaktif = "";
            $nolevel = 1;
            while ($l = mysqli_fetch_array($level)) {
                array_push($listlevel, $l);
                if ($nolevel == 1) {
                    $levelaktif = $l;
                }
                $nolevel++;
            }
            $_SESSION['login'] = $levelaktif;
            $_SESSION['uuid'] = $var['uuid'];
            $_SESSION['username'] = $var['username'];
            $_SESSION['nama'] = $var['nama'];
            $_SESSION['email'] = $var['email'];
            $_SESSION['photo'] = $var['photo'] != "" ? "storage/profile/small_" . $var['photo'] : 'assets/img/avatars/user.png';
            $_SESSION['level'] = $listlevel;
            $_SESSION['levelaktif'] = $levelaktif;

            header('location:index.php?pesan=sukses');
        } else {
            header('location:index.php?menu=login&pesan=error');
        }
    }
} elseif (isset($_GET['level'])) {
    $level = clear(isset($_GET['level']) ? $_GET['level'] : '');
    $querylevel = mysqli_query($con, "SELECT userlevel.level, level.icon as level_icon, level.nama as level_nama, level.kode as level_kode FROM userlevel 
                                      LEFT JOIN level ON userlevel.level = level.uuid
                                      WHERE userlevel.user='".$_SESSION['uuid']."' AND userlevel.level='$level' ");
    $ceklevel = mysqli_num_rows($querylevel);
    if($ceklevel > 0){
        $_SESSION['levelaktif'] = mysqli_fetch_array($querylevel);
        header('location:index.php?pesan=sukseslevel');
    }else{
        header('location:index.php?pesan=gagallevel');
    }
} else {
    header('location:index.php');
}
