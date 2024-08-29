function hideDiv() {
    document.getElementById('trackForm').style.display = "none";
    document.getElementById('loadingGif').style.display = "block";
}

function showDiv() {
    document.getElementById('loadingGif').style.display = "none";
    document.getElementById('trackingInfoContainer').style.display = "block";
    document.getElementById('trackForm').style.display = "block";
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('trackForm');

    if (!form || typeof form === 'undefined') return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault(); // Prevent the default form submission

        const salesOrderNumber = document.getElementById('salesOrderNumber').value.trim().toUpperCase();
        const isInternal = /^(SO|AKL|AKL1|AKL2|AKL3|AKL4|AKL5|AKL11|AKL15|WLG|WLG1|WLG2|CHCH|CHCH1|CHCH2|CHC)/.test(salesOrderNumber);
        const isStarshipit = salesOrderNumber.length === 24;

        if (salesOrderNumber.startsWith("MX")) {
            document.getElementById('externalTrackingContainer').style.display = "none";
            document.getElementById('trackingInfoContainer').style.display = "block";
            const trackingInfo = await fetchMyFastwayTrackingInformation(salesOrderNumber);
            console.log(trackingInfo);
            // Handle the tracking information (e.g., display it on the page)
        } else if (isStarshipit) {
            document.getElementById('externalTrackingContainer').style.display = "none";
            document.getElementById('trackingInfoContainer').style.display = "block";
            await fetchTrackingInformation(salesOrderNumber);
        } else if (isInternal) {
            document.getElementById('externalTrackingContainer').style.display = "none";
            document.getElementById('trackingInfoContainer').style.display = "block";
            await fetchTrackingInformation(salesOrderNumber);
        } else {
            document.getElementById('trackingInfoContainer').style.display = "none";
            document.getElementById('externalTrackingContainer').style.display = "block";
            await renderExternalWidget(salesOrderNumber);
        }
    });
});

async function renderExternalWidget(salesOrderNumber) {
    YQV5.trackSingle({
        YQ_ContainerId: "externalTrackingContainer",
        YQ_Height: 560,
        YQ_Fc: "0",
        YQ_Lang: "en",
        YQ_Num: salesOrderNumber
    });
}

async function fetchMyFastwayTrackingInformation(trackingNumber) {
    try {
        const response = await fetch(`/api/myfastway-tracking?tracking_number=${trackingNumber}`, {
            headers: {
                'Content-Type': 'application/json'
            },
        });

        if (!response.ok) {
            throw new Error('Failed to fetch tracking information');
        }

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

async function fetchTrackingInformation(salesOrderNumber) {
    const container = document.getElementById('trackingInfoContainer');
    container.innerHTML = '';
    hideDiv();

    if (salesOrderNumber) {
        if (salesOrderNumber.length === 24) {
            const starshipitTrackingInfo = await fetchStarshipitTrackingInformation(salesOrderNumber);
            if (starshipitTrackingInfo) {
                await displayStarshipitTrackingInfo(starshipitTrackingInfo);
            } else {
                container.innerHTML = "No Starshipit tracking information available for this order.";
                console.error('No Starshipit tracking information available for this order.');
            }
        } else {
            await displayTrackingInfo(salesOrderNumber);
        }
    } else {
        container.innerHTML = "Please enter a sales order number.";
        console.error('Please enter a sales order number.');
    }
    showDiv();
}
async function fetchTaskEventsByOrderNumber(salesOrderNumber) {
    try {
        const response = await fetch(`/api/task-events?order=${salesOrderNumber}`, {
            headers: {
                'Content-Type': 'application/json'
            },
        });

        if (!response.ok) {
            throw new Error('Failed to fetch task events');
        }

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return [];
    }
}
async function fetchTrackingCredentials() {
    try {
        const response = await fetch('/api/tracking-credentials', {
            headers: {
                'Content-Type': 'application/json'
            },
        });

        if (!response.ok) {
            throw new Error('Failed to fetch tracking credentials');
        }

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

async function fetchStarshipitTrackingInformation(salesOrderNumber) {
    try {
        const credentials = await fetchTrackingCredentials();

        if (!credentials) {
            throw new Error('Tracking credentials not available');
        }

        const response = await fetch(`https://api.starshipit.com/api/track?tracking_number=${salesOrderNumber}`, {
            headers: {
                'Content-Type': 'application/json',
                'StarShipIT-Api-Key': credentials.starshipit_api_key,
                'Ocp-Apim-Subscription-Key': credentials.starshipit_subscription_key,
            },
        });

        if (!response.ok) {
            throw new Error('Failed to fetch Starshipit tracking information');
        }

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}
async function displayStarshipitTrackingInfo(trackingInfo) {
    const container = document.getElementById('trackingInfoContainer');
    let htmlContent = '';

    if (trackingInfo.success && trackingInfo.results) {
        const { results } = trackingInfo;
        const { tracking_events } = results;

        tracking_events.forEach(event => {
            const eventDate = new Date(event.event_datetime).toLocaleDateString('en-UK', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const eventTime = new Date(event.event_datetime).toLocaleTimeString('en-UK', {
                hour: '2-digit',
                minute: '2-digit'
            });

            htmlContent += `
                <div class="tracking-box">
                    <div class="tracking-time-box">
                        <div class="tracking-time">${eventDate}</div>
                        <p>${eventTime}</p>
                    </div>
                    <div class="tracking-location style-three">
                        <span class="dott"></span>
                        <div class="event-details">
                            <strong class="event-title">${event.status}</strong>
                            <p class="event-description">${event.details}</p>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        console.error('No Starshipit tracking information available.');
        htmlContent = `<p>No Starshipit tracking information available for this order.</p>`;
    }

    container.innerHTML = htmlContent;
}
async function displayTrackingInfo(salesOrderNumber) {
    const container = document.getElementById('trackingInfoContainer');
    let htmlContent = '';

    // Fetch task events
    const taskEvents = await fetchTaskEventsByOrderNumber(salesOrderNumber);

    if (taskEvents && taskEvents.length > 0) {
        taskEvents.forEach(event => {
            const eventDate = new Date(event.created_at).toLocaleDateString('en-UK', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const eventTime = new Date(event.created_at).toLocaleTimeString('en-UK', {
                hour: '2-digit',
                minute: '2-digit'
            });

            let eventTitle = event.event;
            let eventDescription = null;

            switch (event.event) {
                case "activate":
                case "assignee_away":
                case "active":
                case "updated":
                case "assignee_near":
                case "create":
                case "near":
                case "unaccept":
                case "unassign":
                    return;    
                case "assign":
                    eventTitle = "Shipment Assigned to Go Deliveries. For any inquiries, please contact Go Deliveries at <a href='tel:0223155638'>0223155638</a> or email <a href='mailto:godeliveries1@gmail.com'>godeliveries1@gmail.com</a>.";
                    break;
                case 'accept':
                    eventTitle = "Package Accepted by Driver. Expected Delivery: 3 Business Days";
                    break;
                case 'in_transit':
                case 'in transit':
                case 'transit':
                    eventTitle = "Shipment Scheduled for Delivery Tomorrow";
                    break;
                case "complete":
                    eventTitle = "Shipment Delivered Successfully";
                    break;
                case "fail":
                    eventTitle = "Delivery Attempt Unsuccessful (see notes for details). Please contact Go Deliveries at <a href='tel:0223155638'>0223155638</a> or email <a href='mailto:godeliveries1@gmail.com'>godeliveries1@gmail.com</a> for assistance.";
                    if (taskEvents?.length && typeof taskEvents?.[0]?.tasks?.metafields?.['instructions:instructions'] !== 'undefined') {
                        eventDescription = `Driver Notes: ${taskEvents?.[0]?.tasks?.metafields?.['instructions:instructions'] || '<i>NIL</i>'}`;
                    }
                    break;
            }

            htmlContent += `
                <div class="tracking-box">
                    <div class="tracking-time-box">
                        <div class="tracking-time">${eventDate}</div>
                        <p>${eventTime}</p>
                    </div>
                    <div class="tracking-location style-three">
                        <span class="dott"></span>
                        <div class="event-details">
                            <strong class="event-title">${eventTitle}</strong>
                            ${eventDescription ? `<p class="event-description">${eventDescription}</p>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        console.error('No task found for this sales order number.');
        htmlContent = `<p>No tracking information available for this task.</p>`;
    }
    
    container.innerHTML = htmlContent;
}