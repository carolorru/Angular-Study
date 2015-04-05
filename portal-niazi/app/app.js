//var appLogin = angular.module('appLogin',['ngCookies']);
var app = angular.module('app',['ngRoute', 'ngCookies', 'ui.bootstrap']);
 

app.config(function($routeProvider, $locationProvider) {
   // remove o # da url
   $locationProvider.html5Mode(true);
 
   $routeProvider
   .when('/portal-niazi-3/home', {
      title       : 'Home',
      templateUrl : 'app/views/home.html',
      controller  : 'HomeCtrl'
   })
   .when('/portal-niazi-3/', {
      title       : 'Login',
      templateUrl : 'app/views/login.html',
      controller  : 'LoginCtrl'
   })
   .when('/portal-niazi-3/separacao', {
      title       : 'Separação',
      templateUrl : 'app/views/separacao.html',
      controller  : 'SeparacaoCtrl'
   })
   .when('/portal-niazi-3/conferencia', {
      title       : 'Conferência',
      templateUrl : 'app/views/conferencia.html',
      controller  : 'ConferenciaCtrl'
   })
   .when('/portal-niazi-3/expedicao', {
      title       : 'Expedição',
      templateUrl : 'app/views/expedicao.html',
      controller  : 'ExpedicaoCtrl'
   })
   .when('/portal-niazi-3/cadastro', {
      title       : 'Edição de Senha',
      templateUrl : 'app/views/cadastro.html',
      controller  : 'CadastroCtrl'
   })
   .otherwise ({ redirectTo: '/portal-niazi-3/' });

})
.run(['$rootScope', '$location', '$cookieStore', '$http',
function ($rootScope, $location, $cookieStore, $http) {

   // keep user logged in after page refresh
   $rootScope.globals = $cookieStore.get('globals') || {};
   console.log($cookieStore, $rootScope.globals);

   if ($rootScope.globals.currentUser) {
      $http.defaults.headers.common['Authorization'] = 'Basic ' + $rootScope.globals.currentUser.authdata; // jshint ignore:line
   }

   $rootScope.$on('$locationChangeStart', function (event, next, current) {
      // redirect to login page if not logged in
      if ($location.path() !== '/portal-niazi-3/' && !$rootScope.globals.currentUser) {
         $location.path('/portal-niazi-3/');
      //redireciona para home quando não tem permissão
      } 
      else {
         var sep = false, exp = false, conf = false;
         for (var i = 0; i < $rootScope.globals.menu.length; i++) {
            switch($rootScope.globals.menu[i].id) {
                case 1:
                    exp = true;
                    break;
                case 2:
                    sep = true;
                    break;
                case 3:
                    conf = true;
                    break;
                //case 4:
                //    pass = true;
                //    break;
            }            
         }; 
         
         if($location.path() == '/portal-niazi-3/separacao' && sep != true){
            $location.path('/portal-niazi-3/');
         }
         if($location.path() == '/portal-niazi-3/expedicao' && exp != true){
            $location.path('/portal-niazi-3/');
         }
         if($location.path() == '/portal-niazi-3/conferencia' && conf != true){
            $location.path('/portal-niazi-3/');
         }
         //if($location.path() == '/portal-niazi-3/cadastro' && pass != true){
         //   $location.path('/portal-niazi-3/');
         //}
      }
   });

}]);