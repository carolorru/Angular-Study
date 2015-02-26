//var appLogin = angular.module('appLogin',['ngCookies']);
var app = angular.module('app',['ngRoute']);
 

app.config(function($routeProvider, $locationProvider) {
   // remove o # da url
   $locationProvider.html5Mode(true);
 
   $routeProvider
   .when('/portal-niase/home', {
      title       : 'Home',
      templateUrl : 'app/views/home.html',
      controller  : 'HomeCtrl'
   })
   .when('/portal-niase/', {
      title       : 'Login',
      templateUrl : 'app/views/login.html',
      controller  : 'LoginCtrl'
   })
   .when('/portal-niase/separacao', {
      title       : 'Separação',
      templateUrl : 'app/views/separacao.html',
      controller  : 'SeparacaoCtrl'
   })
   .when('/portal-niase/conferencia', {
      title       : 'Conferência',
      templateUrl : 'app/views/conferencia.html',
      controller  : 'ConferenciaCtrl'
   })
   .when('/portal-niase/expedicao', {
      title       : 'Expedição',
      templateUrl : 'app/views/expedicao.html',
      controller  : 'ExpedicaoCtrl'
   })
   .otherwise ({ redirectTo: '/portal-niase/' });
});