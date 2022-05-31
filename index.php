<?php
// auto load
spl_autoload_extensions(".php");
function classLoader($class)
{
  $nomeArquivo = $class . ".php";
  $pastas = array(
    "shared/controller",
    "shared/model",
    "public/controller",
    "public/model"
  );
  foreach ($pastas as $pasta) {
    $arquivo = "{$pasta}/{$nomeArquivo}";
    if (file_exists($arquivo)) {
      require_once $arquivo;
    }
  }
}
spl_autoload_register("classLoader");

Session::startSession();
Session::freeSession();

//FrontController
class Aplicacao
{
  static private $app = "/web2_2022";
  static public function run()
  {
    $layout = new Template("public/view/layout.html");
    $layout->set("uri", self::$app);
    if (isset($_GET["class"])) {
      $class = $_GET["class"];
    } else {
      $class = "Login";
    }
    if (isset($_GET["method"])) {
      $method = $_GET["method"];
    } else {
      $method = "";
    }
    if (class_exists($class)) {
      $pagina = new $class;
      if (method_exists($pagina, $method)) {
        $pagina->$method();
      } else {
        $pagina->controller();
      }
      $layout->set("conteudo", $pagina->getMessage());
    }
    echo $layout->saida();
  }
}

Aplicacao::run();