import React from 'react';

const WeatherReports = (props) => {
    if (!props.weatherReports || !Object.keys(props.weatherReports).length) {
        return null;
    }
    return (
        <div>
            <h3>Weather Report{ props.weatherReports.length > 1 ? 's' : '' }:</h3>
            <ul>
                {Object.values(props.weatherReports).map((weatherReport, index) => {
                    return (
                        <li key={index}>
                            <strong>{weatherReport.main}:</strong>
                            {' '}
                            {weatherReport.description}
                            {' '}
                            <img src={'//openweathermap.org/img/w/' + weatherReport.icon + '.png'}
                        />
                        </li>
                    );
                })}
            </ul>
        </div>
    );
};

export default WeatherReports;
