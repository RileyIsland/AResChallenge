import React from 'react';
import Errors from '../general/errors.js';
import KeyValueList from '../general/key-value-list.js';
import WeatherReports from './weather-reports.js';

const Results = (props) => (
    <div>
        <h2>Results for {props.results.zip}</h2>
        {(props.results.errors && props.results.errors.length
            ? (<Errors errors={props.results.errors} />)
            : (
                <div>
                    <KeyValueList title="Location Data" items={props.results.location_data} />
                    <KeyValueList title="General Weather" items={props.results.general_weather} />
                    <WeatherReports weatherReports={props.results.weather_reports} />
                </div>
            )
        )}
    </div>
);

export default Results;
