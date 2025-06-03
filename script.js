document.addEventListener('DOMContentLoaded', () => {
    const timeToH1Input = document.getElementById('time-to-h1');
    const interval1Input = document.getElementById('interval1');
    const interval2Input = document.getElementById('interval2');
    const interval3Input = document.getElementById('interval3');
    const interval4Input = document.getElementById('interval4');
    const interval5Input = document.getElementById('interval5');
    const interval6Input = document.getElementById('interval6');
    const interval7Input = document.getElementById('interval7');
    const interval8Input = document.getElementById('interval8');
    const interval9Input = document.getElementById('interval9');
    const runInInput = document.getElementById('run-in');
    const calculateBtn = document.getElementById('calculate-btn');
    const touchdownTimesUl = document.getElementById('touchdown-times');
    const finalTimeSpan = document.getElementById('final-time');

    calculateBtn.addEventListener('click', () => {
        const timeToH1 = parseFloat(timeToH1Input.value) || 0;
        const intervals = [
            parseFloat(interval1Input.value) || 0,
            parseFloat(interval2Input.value) || 0,
            parseFloat(interval3Input.value) || 0,
            parseFloat(interval4Input.value) || 0,
            parseFloat(interval5Input.value) || 0,
            parseFloat(interval6Input.value) || 0,
            parseFloat(interval7Input.value) || 0,
            parseFloat(interval8Input.value) || 0,
            parseFloat(interval9Input.value) || 0,
        ];
        const runInTime = parseFloat(runInInput.value) || 0;

        // Clear previous results
        touchdownTimesUl.innerHTML = '';
        finalTimeSpan.textContent = '';

        if (timeToH1 <= 0) {
            alert("Time to 1st Hurdle must be greater than 0.");
            return;
        }

        let cumulativeTime = timeToH1;
        const touchdownTimes = [cumulativeTime];

        // Calculate touchdown times for H1-H10
        let touchdownListItem = document.createElement('li');
        touchdownListItem.textContent = `Hurdle 1: ${cumulativeTime.toFixed(2)}s`;
        touchdownTimesUl.appendChild(touchdownListItem);

        for (let i = 0; i < intervals.length; i++) {
            if (intervals[i] <= 0) {
                alert(`Interval ${i + 1}-${i + 2} must be a positive number.`);
                // Clear results if any interval is invalid
                touchdownTimesUl.innerHTML = '';
                finalTimeSpan.textContent = '';
                return;
            }
            cumulativeTime += intervals[i];
            touchdownTimes.push(cumulativeTime);
            touchdownListItem = document.createElement('li');
            touchdownListItem.textContent = `Hurdle ${i + 2}: ${cumulativeTime.toFixed(2)}s`;
            touchdownTimesUl.appendChild(touchdownListItem);
        }

        // Calculate and display final time
        const finalTime = cumulativeTime + runInTime;
        if (runInTime <= 0) {
            alert("Run-in time must be a positive number.");
            // Clear results if run-in time is invalid
            touchdownTimesUl.innerHTML = '';
            finalTimeSpan.textContent = '';
            return;
        }
        finalTimeSpan.textContent = finalTime.toFixed(2) + 's';
    });
});
