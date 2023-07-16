# Introduction

In link <https://developer.dhl.com/api-reference/dhl-express-mydhl-api#operations-rating-exp-api-rates-many> you can find more information about DHL EXPRESS requests and responses.

There are two ways to consume the **DHL Express api**, one as a **development mode** and the other as a **production mode.**

For requests and responses in **development mode**, you should edit the **environment variable APP_ENV**, with the value of **local**

For requests and responses in **production mode**, you should edit the **environment variable APP_ENV**, with the value **production**

# Configuration of requests and response

For requests and responses in both environments, five environment variables must be created in the **.ENV** file

- **USERNAME_DHL**
- **PASSWORD_DHL**
- **ACCOUNTS_NUMBER_DHL**
- **PROD_DH**. With the value of <https://express.api.dhl.com/mydhlapi>
- **TEST_DHL**. With the value of <https://express.api.dhl.com/mydhlapi/test>

For the values of **USERNAME_DHL, PASSWORD_DHL and ACCOUNTS_NUMBER_DHL**, you should consult your account information at <https://developer.dhl.com/api-reference/dhl-express-mydhl-api#operations-rating-exp-api-rates-many>.

# Base service for requests and responses

To handle _DHL requests and responses_, we created a service, called **ApiDHL**, with a global exception, to handle errors from within **Handler.php.**. Using the _Laravel dependency_, called **Illuminate\\Support\\Facades\\Http**, for the http client requests.

![](https://files.readme.io/346d2c8-image.png)

![](https://files.readme.io/1e8e68c-image.png)

The **ApiDhl class**, has as **url property**, which at the time of instantiating the class, you must add the value of the _url_

# Service Rates

The properties of the ServiceGetRates class are:

![](https://files.readme.io/4c7f00c-image.png)

The getRates property, is to make the request to DHL Rates, this property builds the body, as requested by the api.

![](https://files.readme.io/90b5c58-image.png)

# Service Shipments

To create DHL shipment, create an entity, to format the request properties. Final class Shipment

![](https://files.readme.io/c10516c-image.png)

![](https://files.readme.io/f1f116e-image.png)

Finally to get the sorted array, we call the getDataArray method

![](https://files.readme.io/b86deb9-image.png)

Then, in the **ServiceShipments class**, two methods are created, one to **create the shipment** and **another to obtain the pdf.**

**Store method**, creates the instance of the entity, passing the values of the request, and calls the **getDataArray** method, for the request to the

![](https://files.readme.io/bcbcd72-image.png)

To obtain the pdf, you must pass the properties **shipmentTrackingNumber, typeCode, pickupYearAndMonth** to the **getImage method**.

![](https://files.readme.io/79849e8-image.png)