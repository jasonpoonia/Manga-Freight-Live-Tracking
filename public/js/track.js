// Function to hide the form and show the loading gif
function hideDiv() {
    document.getElementById('trackForm').style.display = "none";
    document.getElementById('loadingGif').style.display = "block";
}

// Function to show the form and hide the loading gif
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
        hideDiv(); // Hide the form and show the loading gif

        // Get the input value and use it directly
        const salesOrderNumber = document.getElementById('salesOrderNumber').value.trim().toUpperCase();

        const isInternal = /^(SO|Kiwi Home Store|S|FP|#|TSB|AKL|AKL1|AKL2|AKL3|AKL4|AKL5|AKL11|AKL15|WLG|WLG1|WLG2|CHCH|CHCH1|CHCH2|CHC)/.test(salesOrderNumber.split('/')[0]);

        try {
            if (isInternal) {
                document.getElementById('externalTrackingContainer').style.display = "none";
                document.getElementById('trackingInfoContainer').style.display = "block";
                await fetchAndDisplayInternalTracking(salesOrderNumber);
            } else {
                document.getElementById('trackingInfoContainer').style.display = "none";
                document.getElementById('externalTrackingContainer').style.display = "block";
                await renderExternalWidget(salesOrderNumber);
            }
        } catch (error) {
            console.error('An error occurred:', error);
            document.getElementById('trackingInfoContainer').innerHTML = "An error occurred while fetching tracking information. Please try again later.";
        } finally {
            showDiv(); // Always show the form and hide the loading gif
        }
    });
});

async function fetchAndDisplayInternalTracking(originalSalesOrderNumber) {
    let trackingInfo = await fetchTaskEventsByOrderNumber(originalSalesOrderNumber);

    // If no results, try without suffix
    if (!trackingInfo || trackingInfo.length === 0) {
        const withoutSuffix = originalSalesOrderNumber.split('/')[0];
        trackingInfo = await fetchTaskEventsByOrderNumber(withoutSuffix);
    }

    // If still no results, try without the 'SO' prefix
    if (!trackingInfo || trackingInfo.length === 0) {
        const withoutPrefix = originalSalesOrderNumber.replace(/^SO/, '').split('/')[0];
        trackingInfo = await fetchTaskEventsByOrderNumber(withoutPrefix);
    }

    if (trackingInfo && trackingInfo.length > 0) {
        await displayTrackingInfo(trackingInfo);
    } else {
        document.getElementById('trackingInfoContainer').innerHTML = "<p>No tracking information available for this order number.</p>";
    }
}

async function renderExternalWidget(salesOrderNumber) {
    YQV5.trackSingle({
        YQ_ContainerId: "externalTrackingContainer",
        YQ_Height: 600,
        YQ_Fc: "0",
        YQ_Lang: "en",
        YQ_Num: salesOrderNumber
    });

    // Add this code to ensure the iframe is 800px high
    setTimeout(() => {
        const iframe = document.querySelector('#externalTrackingContainer iframe');
        if (iframe) {
            iframe.style.height = '800px';
            iframe.style.minHeight = '800px';
        }
    }, 1000); // Wait for 1 second to ensure the iframe has loaded
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

async function displayTrackingInfo(taskEvents) {
    const container = document.getElementById('trackingInfoContainer');
    let htmlContent = '';

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
                    eventTitle = "Out for Delivery";
                    eventDescription = "Your package is out for delivery. Expected arrival: Today or Tomorrow.";
                    break;
                case "cancel":
                    eventTitle = "Shipment Cancelled";
                    eventDescription = "This shipment has been cancelled. If you believe this is an error, please contact customer support.";
                    break;
                case "away":
                case "assignee_away":
                    return;
                case "active":
                    eventTitle = "Out for Delivery";
                    eventDescription = "Your package is out for delivery. Expected arrival: Today or Tomorrow.";
                    break;
                case "updated":
                    return;
                case "assignee_near":
                case "near":
                    eventTitle = "Driver Approaching";
                    eventDescription = "The delivery driver is nearing your location. Please ensure someone is available to receive the package.";
                    break;
                case "create":
                    eventTitle = "Shipment Created";
                    eventDescription = "Your shipment has been created in our system and is awaiting further processing.";
                    break;
                case "unaccept":
                case "unassign":
                    return;
                case "assign":
                    eventTitle = "Shipment Assigned to Manga Freight";
                    eventDescription = "Your shipment has been assigned to Manga Freight for delivery. For any inquiries, please contact Manga Freight at <a href='tel:+64096105999'>09 610 5999</a> or email <a href='mailto:info@mangafreight.co.nz'>info@mangafreight.co.nz</a>.";
                    break;
                case 'accept':
                    eventTitle = "Package Picked Up by Driver";
                    eventDescription = "Your package has been picked up and is on its way. Estimated Delivery: 3-5 Business Days for Main Cities, 4-8 Business Days for Other Areas.";
                    break;
                case 'in_transit':
                case 'transit':
                    eventTitle = "Shipment in Transit";
                    eventDescription = "Your package is currently in transit to the local depot for further processing and delivery.";
                    break;
                case 'restart':
                    eventTitle = "Delivery Reattempt Scheduled";
                    eventDescription = "A previous delivery attempt was unsuccessful. Our driver will make another delivery attempt soon.";
                    break;
                case "complete":
                case "completed":
                    eventTitle = "Shipment Delivered Successfully";
                    eventDescription = "Your package has been delivered successfully. Thank you for using our service!";
                    break;
                case "fail":
                    eventTitle = "Delivery Attempt Unsuccessful";
                    eventDescription = "Unfortunately, our delivery attempt was unsuccessful. Please contact Manga Freight at <a href='tel:64096105999'>09 610 5999</a> or email <a href='mailto:info@mangafreight.co.nz'>info@mangafreight.co.nz</a> for assistance.";
                    if (taskEvents?.length && typeof taskEvents?.[0]?.tasks?.metafields?.['instructions:instructions'] !== 'undefined') {
                        eventDescription += `<br><br>Driver Notes: ${taskEvents?.[0]?.tasks?.metafields?.['instructions:instructions'] || '<i>NIL</i>'}`;
                    }
                    break;
                default:
                    eventTitle = event.event.charAt(0).toUpperCase() + event.event.slice(1);
                    eventDescription = "Status update received for your shipment.";
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