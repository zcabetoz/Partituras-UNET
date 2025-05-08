import Routing from 'fos-router';

export default function UserController($http) {
    let $ctrl = this;

    const url = Routing.generate('test_connect');

    $ctrl.data = {
        name: 'Carlos Serrano',
        age: 30,
        email: 'cabeto@gmail.com'
    }

    $http.post(url, {param: $ctrl.data}).then(function (response) {
        console.log(response.data)
        $ctrl.res = response.data.response;
    });


    $ctrl.name = 'Carlos Serrano';
    console.log($ctrl.name);
}