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
                <MomentList moments={moments} />
            </div>
        );
    },

    addMoment: function(moment) {
        this.setState({
            moments: [moment].concat(this.state.moments)
        });
    },

    collectMoment: function(s_id) {
        this.setState({}, this.state, {
            moments: this
        });
    }
});

module.exports = App;