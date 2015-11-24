var configureStore = require('./store/configureStore');
var addMoment = require('./actions/moments').addMoment;

var store = configureStore();

console.log(store.getState());

var unsubscribe = store.subscribe(function() {
    console.log(store.getState());
});

store.dispatch(addMoment({
    avatar: 'avatar1',
    name: 'name1',
    text: 'text1',
    pic: ['pic1'],
    createTime: '2015-10-30'
}));

unsubscribe();
