var React = require('react');
var Publisher = require('../components/Publisher');
var MomentList = require('../components/MomentList');
var assign = require('object-assign');

var App = React.createClass({
    getInitialState: function() {
        return {
            moments: this.props.moments
        };
    },

    render: function() {
        var moments = this.state.moments;
        console.log(moments);
        return (
            <div>
                <Publisher addMoment={this.addMoment} />
                <MomentList
                    moments={moments}
                    collectMoment={this.collectMoment}
                    thumbMoment={this.thumbMoment}
                />
            </div>
        );
    },

    addMoment: function(moment) {
        this.setState({
            moments: [moment].concat(this.state.moments)
        });
    },

    collectMoment: function(s_id) {
        var newState = assign({}, this.state);
        newState.moments = newState.moments.map(function(item, index) {
                if (item.s_id == s_id) {
                    item.collected = item.collected == 1 ? 0 : 1;
                };
                return item;
        });
        this.setState(newState);
    },

    thumbMoment: function(s_id) {
        var newState = assign({}, this.state);
        newState.moments = newState.moments.map(function(item, index) {
                if (item.s_id == s_id) {
                    if (item.thumbed == 1) {
                        item.thumbed = 0;
                        item.tb_count--;
                    } else {
                        item.thumbed = 1;
                        item.tb_count++;
                    }
                };
                return item;
        });
        this.setState(newState);
    }
});

module.exports = App;