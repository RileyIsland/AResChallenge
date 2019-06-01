import React from 'react';

const Errors = (props) => {
    if (!props.errors.length) {
        return null;
    }
    return (
        <div>
            Error{ props.errors.length > 1 ? 's' : '' }:
            <ul>
                {props.errors.map((error, index) => {
                    return (<li key={index}>{error}</li>);
                })}
            </ul>
        </div>
    );
};

export default Errors;
