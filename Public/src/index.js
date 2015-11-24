var React = require('react');
var ReactDOM = require('react-dom');
var Provider = require('react-redux').Provider;
var configureStore = require('./store/configureStore');
var App = require('./containers/App');
var addMoment = require('./actions/moments').addMoment;

var store = configureStore();
var unsubscribe = store.subscribe(function() {
	console.log(store.getState());
});

ReactDOM.render(
    <Provider store={store}>
        <App />
    </Provider>,
    document.getElementById('moment-container')
);
