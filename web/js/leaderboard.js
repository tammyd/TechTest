
function LeaderboardCtrl($scope, $http) {

    $http.get('/leaderboard').success(function(data) {
        $scope.leaderboard = data;
    });
    $scope.increment = 5;

    $scope.doSelect = function(index) {
        var elem = $('div.player.person-'+index)
        $('div.player').removeClass('selected');
        $('div.none').removeClass('none');
        elem.addClass('selected');
        $('div.name').text($('div.player.selected .name').text());
        $('div.details > button').show()
        $('div.details > input.inc').show()
    };

    $scope.doUpdate = function() {
        var $curr = $('div.player.selected .score');
        var $score = parseInt($curr.text());
        $curr.text($score + $scope.increment);

        var data = {
            name: $('div.player.selected .name').text(),
            points: $scope.increment
        };
        $http.post('/update', data).success(function(data) {
            $('div.player.selected .score').text(data.rows.value)
        });
    }



}