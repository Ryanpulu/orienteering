#orienteering_rest
#orienteering rest API
#ignore config.toml,project.txt,database.php,log,cache,redis.php,if need ,contact author
#author : Ryan
#author mail : mailto:ryanpulu@outlook.com
#web server : nginx ; dataBase : mysql-5.5 , redis ; database driver: PDO; language: PHP-5.3 ;  frame : CodeIgniter ;
# frame note: 
#1丶classes:
#the autoload class list:CI_Cache, this class object declared in the CI_Controller 
#for example $this->cache is declared in CI_Controller
#2丶Conf:
#the app Config is the config.toml,it be analyzed by the toml_lib.php that will be loading when the CI_Controller is loading,if the app Config is not exist in the redis ,then the CI_Controller will call the method in the toml_lib.php to analyzed the config.toml,and save it in redis.
#3丶code 
#charset=>utf-8
