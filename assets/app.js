import angular from 'angular';
import './styles/app.css';
import 'admin-lte';
import 'overlayscrollbars/styles/overlayscrollbars.css';
import 'bootstrap';
import "angular-ui-bootstrap";
import './services/partituras-services';

import UsuarioController from './controllers/user-controller';
import MenuController from "./controllers/menu-controller";
import SecurityController from "./controllers/security-controller";

angular.module('MyApp', ['ui.bootstrap', 'partituras.services'])
    .controller('UsuarioController', UsuarioController)
    .controller('MenuController', MenuController)
    .controller('SecurityController', SecurityController)

