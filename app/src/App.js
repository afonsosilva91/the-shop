import React, { Component } from 'react';
import logo from './logo.svg';
import './App.css';
import OrdersList from "./components/OrdersList";

class App extends Component {
  render() {
    return (
      <div className="App">
        <header className="App-header">
          <img src={logo} className="App-logo" alt="logo" />
          <h1 className="App-title">The Shop</h1>

          <p className="App-intro">
            Welcome to my shop, fell free to hack!
          </p>
        </header>
       

        <OrdersList />
      </div>
    );
  }
}

export default App;
