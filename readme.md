# Lexconnect
## Technical Background
Lexconnect was created out of my colleague and I's fustration of the legal support services industry (we were both paralegals). This is first attempt at developing an application. I've worked with PHP before but I never built anything from scratch. There were a lot of struggles with learning Laravel, a PHP framework, JavaScript, and git. Additionally, I learned the hard way why a lot of best practices have been established, such as modular code.

## Application Overview
Lexconnect gives law firms the convenience of a nationwide process server network without the time and expense of maintaining it. The application is easily customizable to a clients specifications. Paralegals and lawyers no longer have to worry about communicating jurisdictional requirements to servers. Servers are prompted through the application to complete the job while conform to any requirements set by the client or jurisdiction.

Process servers can use Lexconnect to help manage and grow their business. Servers can receive additional jobs without advertising or cold calling law firms.  The application automatically favors responsive, as defined by completing jobs quickly and post frequent updates on jobs, servers by referring them more jobs. Servers still have complete control of their business by accepting or rejecting any job without any penalty. 


# Possible Updates
## Matching Algorithm
Working on the matching algorithm that is used to assign the servers to jobs. I would like to use a Bayseian average to improve the rating system for the servers. Right now servers are rated now using a simple average. Additionally, there are more factors when matching a server to a job. The server cannot be selected on rating or proximity alone, there needs to be weighted combination of the two, and possibly other factors. Lastly, the algorithm needs to be able to account for the differences of the densley populated urban core and the sparsley populated rural areas.

## Mobile Application
A mobile application is absolutely necessary for Lexconnect. Process servers, by the nature of their work, are always out in the field. They need to be able to document everything as they try to complete a job. Some jurisdictions, such as New York City, require attempts to be geocoded to prevent fraud. Additionaly, servers can and do work in remote areas with spotty or non existant cell coverage. They will still need to be able to use the application while offline. The application will need to both cache data for use offline and data entered to be submitted when the user comes back online.   
