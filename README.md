# Example API

This API is active at endpoint http://api.chadlinden.com

Manager user: 
* email: man@manager.com
* password: manager

Employee user
* email emp@employee.com
* password: employee

#### To Login ####
`POST example.com/v1/login or POST example.com/login`

 Login using email & password 
 Once authenticated you will receive a token to use for each subsequent request.
 
 That token should be passed in the header 
 `token: your_api_access_token`

#### List a user's shifts during given period ####
`GET example.com/v1/shifts/{user}`

##### Required input #####
| input       | type    | description       |
| ----------- | ------- |  ---------------- |
| user        | int     | user's id         |
| email       | string  | user's email      |
| password    | string  |                   |
| start       | date    | 'Y-m-d H:i:s'     |
| end         | date    | 'Y-m-d H:i:s'     |  

Example request `GET example.com/v1/shifts/2069?start=2016-06-20 00:00:00&end=2016-06-30 00:00:00`

Returns 

```
{
  "start": "2016-06-20 00:00:00",
  "end": "2016-06-30 00:00:00",
  "user_id": "2069",
  "assigned": [
    {
      "id": 432,
      "manager_id": 2070,
      "employee_id": 2069,
      "break": 1.17,
      "start_time": "Mon, 27 Jun 2016 08:30:13 -0400",
      "end_time": "Mon, 27 Jun 2016 12:30:13 -0400",
      "length": "4"
    },
  ]
}
```

##### Optional input #####

| input       | type    | description                                   |
| ----------- | ------- |  ----------------                             |
| coworkers   | boolean | list coworkers for each shift                 |

```
    ...
    ],
    "coworkers": [
      {
        "name": "Miss Bria Zemlak"
      },
    ]
```

| input       | type    | description                                   |
| ----------- | ------- |  ----------------                             |
| manager     | boolean | include manager's information for each shift  |

```
    ...
    ],
    "manager": [
      {
        "id": 2070,
        "name": "Cathrine Graham",
        "role": "manager",
        "email": "kbernier@yost.com",
        "phone": "+1 (943) 936-8365",
        "password": null,
        "created_at": "2016-06-24 20:24:01",
        "updated_at": "2016-06-24 20:24:01"
      }
    ]
```

| input       | type    | description                                   |
| ----------- | ------- |  ----------------                             |
| available   | boolean | append a list of open shifts to end of list   |

```
    ...
    ],
    "available": [
      {
        "id": 445,
        "manager_id": 2070,
        "employee_id": 0,
        "break": 0.51,
        "start_time": "Wed, 29 Jun 2016 12:00:18 -0400",
        "end_time": "Wed, 29 Jun 2016 22:00:18 -0400",
        "length": "10"
      },
    ]
```

#### List user information ####
`GET example.com/v1/user/{user}`

##### Required input #####
| input       | type    | description       |
| ----------- | ------- |  ---------------- |
| user        | int     | user's id         |

Example request `GET example.com/v1/user/2069`

```
{
  "user": {
    "id": "2069",
    "name": "Assunta Ruecker MD",
    "role": "employee",
    "email": "sauer.bernadine@yahoo.com",
    "phone": "707.264.7204",
    "created_at": "Fri, 24 Jun 2016 20:24:00 -0400",
    "updated_at": "Fri, 24 Jun 2016 20:24:00 -0400"
  },
  "summary": [
    {
      "shift": {
        "id": 606,
        "manager_id": 123,
        "employee_id": 2069,
        "break": 0.5,
        "start_time": "Tue, 21 Jun 2016 15:30:00 -0400",
        "end_time": "Tue, 21 Jun 2016 20:30:00 -0400",
        "length": "5"
      },
      "cumulative": 5
    },
    {
      "shift": {
        "id": 607,
        "manager_id": 123,
        "employee_id": 2069,
        "break": 0.5,
        "start_time": "Wed, 22 Jun 2016 08:00:00 -0400",
        "end_time": "Wed, 22 Jun 2016 16:00:00 -0400",
        "length": "8"
      },
      "cumulative": 13
    }
  ]
}
```

The summary shows the shifts since the beginning of the week, with a running tally ( `"cumulative"` ) of the accumulated hours worked.

#### Create new shift ####
`POST example.com/v1/shift/create`

##### Required input #####
| input       | type    | description                               |
| ----------- | ------- |  ---------------------------------------- |
| employee_id | int     | id of the employee being assigned         |
| break       | float   | length of scheduled shift's break         |
| start_time  | date    | 'Y-m-d H:i:s'                             |
| end_time    | date    | 'Y-m-d H:i:s'                             |

Example request `POST example.com/v1/shift/create?employee_id=2069&break=0.5&start_time=2016-06-30 08:30:00&end_time=2016-06-30 18:30:00`

```
{
  "created": {
    "employee_id": "2069",
    "break": "0.5",
    "start_time": "2016-06-30 08:30:00",
    "end_time": "2016-06-30 18:30:00",
    "manager_id": 2071,
    "updated_at": "2016-06-26 16:49:31",
    "created_at": "2016-06-26 16:49:31",
    "id": 615
  }
}
```

#### Update a shift ####
`PUT example.com/v1/shift/update`

##### Required input #####
| input       | type    | description                               |
| ----------- | ------- |  ---------------------------------------- |
| shift_id    | int     | id of the shift being updated             |

You may update any one or all of these keys in a single request

| input       | type    | description                               |
| ----------- | ------- |  ---------------------------------------- |
| employee_id | int     | id of the employee being assigned         |
| break       | float   | length of scheduled shift's break         |
| start_time  | date    | 'Y-m-d H:i:s'                             |
| end_time    | date    | 'Y-m-d H:i:s'                             |

Example request `PUT example.com/v1/shift/update?shift_id=615&start_time=2016-06-30 11:30:00`

Response: 

```
{
  "updated": {
    "id": 615,
    "manager_id": 2071,
    "employee_id": "0000002069",
    "break": "0.50",
    "start_time": "2016-06-30 11:30:00",
    "end_time": "2016-06-30 18:30:00",
    "created_at": "2016-06-26 16:49:31",
    "updated_at": "2016-06-26 17:14:16"
  },
  "diff": {
    "start_time": "2016-06-30 11:30:00",
    "updated_at": "2016-06-26 18:07:33"
  }
}
```