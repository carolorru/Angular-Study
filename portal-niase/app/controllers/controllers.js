app.controller('HomeCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});
 
app.controller('LoginCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});


app.controller('SeparacaoCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://webtalk.com.br/uperp/pedidos/a-separar').success(function(data){
        json = data;
        $scope.aSeparar = json;
    });
    $http.get('http://webtalk.com.br/uperp/pedidos/em-separacao').success(function(data){
        json = data;
        $scope.emSeparacao = json;
    });
    $http.get('http://webtalk.com.br/uperp/pedidos/separados').success(function(data){
        json = data;
        $scope.separados = json;
    });
    $rootScope.activetab = $location.path();
});



app.controller('ConferenciaCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://webtalk.com.br/uperp/conferencia/a-conferir').success(function(data){
        json = data;
        $scope.aConferir = json;
    });
    $http.get('http://webtalk.com.br/uperp/conferencia/em-conferencia').success(function(data){
        json = data;
        $scope.emConferencia = json;
    });
    $http.get('http://webtalk.com.br/uperp/conferencia/conferidos').success(function(data){
        json = data;
        $scope.conferidos = json;
    });
    $rootScope.activetab = $location.path();
});

app.controller('ExpedicaoCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://webtalk.com.br/uperp/pedidos/a-embalar').success(function(data){
        json = data;
        $scope.aEmbalar = json;
    });
    $http.get('http://webtalk.com.br/uperp/pedidos/embalando').success(function(data){
        json = data;
        $scope.embalando = json;
    });
    $http.get('http://webtalk.com.br/uperp/pedidos/embalados').success(function(data){
        json = data;
        $scope.embalados = json;
    });
    $rootScope.activetab = $location.path();
});