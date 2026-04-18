{pkgs}: {
  deps = [
    pkgs.php82
    pkgs.nginx
    pkgs.php82Extensions.pdo_pgsql
    pkgs.apacheHttpd
  ];
}
