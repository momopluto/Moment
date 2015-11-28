// var React = require('react');
// var ReactDOM = require('react-dom');
// var Provider = require('react-redux').Provider;
// var configureStore = require('./store/configureStore');
// var App = require('./containers/App');
// var addMoment = require('./actions/moments').addMoment;

// var state = {
// 	moments: JSON.parse(window.json_data)
// };

// var store = configureStore(state);
// var unsubscribe = store.subscribe(function() {
// 	console.log(store.getState());
// });

// ReactDOM.render(
//     <Provider store={store}>
//         <App />
//     </Provider>,
//     document.getElementById('moment-container')
// );

var React = require('react');
var ReactDOM = require('react-dom');
var App = require('./containers/App');

ReactDOM.render(
    <App moments={JSON.parse(window.json_data)} />,
    document.getElementById('moment-container')
);