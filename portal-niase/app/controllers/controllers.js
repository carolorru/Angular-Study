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



app.controller('ConferenciaCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});

app.controller('ExpedicaoCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});