import angular from 'angular';
import './styles/app.css';
import 'admin-lte';
import 'overlayscrollbars/styles/overlayscrollbars.css';
import 'bootstrap';

import UsuarioController from './controllers/user-controller';

angular.module('MyApp', [])
    .controller('UsuarioController', UsuarioController);
