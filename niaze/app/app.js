var appLogin = angular.module('appLogin',['ngCookies']);
var app = angular.module('app',['ngRoute']);
 

app.config(function($routeProvider, $locationProvider) {
   // remove o # da url
   $locationProvider.html5Mode(true);
 
   $routeProvider

   // para a rota '/', carregaremos o template home.html e o controller 'HomeCtrl'
   .when('/portal-niase/separacao', {
      templateUrl : 'app/views/separacao.html',
      controller     : 'HomeCtrl',
   })
 
   // para a rota '/sobre', carregaremos o template sobre.html e o controller 'SobreCtrl'
   .when('/portal-niase/sobre', {
      templateUrl : 'app/views/sobre.html',
      controller  : 'SobreCtrl',
   })
 
   // para a rota '/contato', carregaremos o template contato.html e o controller 'ContatoCtrl'
   .when('/portal-niase/contato', {
      templateUrl : 'app/views/contato.html',
      controller  : 'ContatoCtrl',
   })
 
   // caso não seja nenhum desses, redirecione para a rota '/'
   .otherwise ({ redirectTo: '/portal-niase/' });
});