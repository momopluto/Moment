var React = require('react');
var Moment = require('./Moment');

var MomentList = React.createClass({
	propTypes: {
		moments: React.PropTypes.array.isRequired
	},

	render: function() {
		var momentList = this.props.moments.map(function(item, index) {
			return (
				<Moment
					key={index}
					moment={item}
					collectMoment={this.props.collectMoment}
					thumbMoment={this.props.thumbMoment}
				/>
			);
		}.bind(this));

		return (
			<div>{momentList}</div>
		);
	}
});

module.exports = MomentList;
