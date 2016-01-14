# intercom-events

A set of PHP scripts used to obtain a CSV of user events per user ID from the Intercom Events API (not currently a feature in the API).

## Why I did this and problem domain

I had a data science project where I wanted to do user retention analysis using various algorithms e.g. logistic regression, random forest etc. - what was it that made a user "stick" in an app. I was using Intercom data of user IDs and different information for each user, including the number of Web Sessions (my `y` value).

The standard Intercom CSV export allows you to export "attributes" of a user  - things like Country or Browser, and perhaps some custom attributes defined. However, you *cannot* do a CSV export of the "count" of events per user - things like "This user has clicked the PDF exports button 54 times in his/her lifetime vs. 23 for the other user".

---

I believed that knowing this information as a feature vector might be useful, especially for data driven product managers using Intercom. For example, if you know that a user transitions to becoming "Active" the moment he/she clicks the enterprise page 6 times, you could structure your roadmap around that information.

This set of scripts (including how I got to it) allow you to have an unlimited memory limit to get a CSV with user ID on the left column, and event name on the right column. This allows you to get a count of events per user, by user type, using a Pivottable. You can also make the script quicker by filtering by the event names you care about.

## Thanks to:

Thanks to the Intercom technical support team for helping me figure this out, especially [Ed Fricker](https://github.com/edkellena) and [Matthew Odette](https://github.com/Matthew-Odette).
