import React, { Component } from 'react';
import ReactDOM from "react-dom";
import { Route, NavLink, HashRouter } from "react-router-dom";

import logo from './logo.svg';
import './App.css';

import OrdersList from "./components/OrdersList";
import OrderDetails from "./components/OrderDetails";

class App extends Component {
  render() {
    return (
        <HashRouter>
            <div className="App">
                <header className="App-header">
                    <img src={logo} className="App-logo" alt="logo" />
                    <h1 className="App-title">The Shop</h1>
                    <p className="App-intro">Welcome to my shop, fell free to hack!</p>
                </header>

                <div className="content">
                    <Route exact path="/" component={OrdersList}/>
                    <Route path="/details" component={OrderDetails}/>
                </div>
            </div>
        </HashRouter>
    );
  }
}

export default App;
