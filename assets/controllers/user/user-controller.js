import Routing from 'fos-router';

export default function UserController($http) {
    let $ctrl = this;

    const url = Routing.generate('test_connect');

    let data = {
        name: 'Carlos Serrano',
        age: 30,
        email: 'cabeto@gmail.com'
    }

    $http.post(url, data).then(function (response) {
        $ctrl.data = response.data;
    });
}