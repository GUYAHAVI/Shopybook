# -*- coding: utf-8 -*-
"""
Data Dictionary for 2016 MSME Survey Dataset
Maps column names to their descriptions
"""

DATA_DICTIONARY = {
    'key3': 'Business owner id (to identify the owner/s training section)',
    'key2': 'Unique Submission ID for the Establishment',
    'ls_index': 'Business owner line number',
    'a_41': 'Business ID',
    'county11': 'County code',
    'eb01_1': 'Section',
    'eb01_2': 'ISIC Division selection',
    'eb03': 'Business location',
    'eb04': 'Site appropriateness for getting customers',
    'eb05': 'Biggest risk of tenancy to business site',
    'eb06': 'Security provider for business',
    'eb07': 'Type of main structure',
    'eb08': 'Nature of ownership of business site',
    'eb09': 'Nature of occupancy of business',
    'eb10': 'Number of months per year',
    'eb11': 'Number of days per week',
    'eb12': 'Number of hours per day',
    'eb13': 'Number of businesses',
    'eb14_1': 'Section',
    'eb14_2': 'ISIC Division selection',
    'eb15': 'Number of businesses',
    'eb16_1': 'Main decision maker for business',
    'eb17': 'Business changed main activity in last 12 months',
    'eb18_1': 'Section',
    'eb18_2': 'ISIC Division selection',
    'eb19': 'Reason for changing from previous business',
    'ec01': 'Number of male working owners',
    'ec02': 'Number of female working owners',
    'hhe_fulltime': 'Employs FULL TIME PAID employees',
    'ec03': 'Full time paid employees - Male - 5-12 years',
    'ec04': 'Full time paid employees - Female - 5-12 years',
    'ec05': 'Full time paid employees - Male - 13-15 years',
    'ec06': 'Full time paid employees - Female - 13-15 years',
    'ec07': 'Full time paid employees - Male - 16-17 years',
    'ec08': 'Full time paid employees - Female - 16-17 years',
    'ec09': 'Full time paid employees - Male - 18-35 years',
    'ec10': 'Full time paid employees - Female - 18-35 years',
    'ec11': 'Full time paid employees - Male - 36+ years',
    'ec12': 'Full time paid employees - Female - 36+ years',
    'hhe_unpaid_fam': 'Employs UNPAID FAMILY workers',
    'ec13': 'Unpaid family workers - Male - 5-12 years',
    'ec14': 'Unpaid family workers - Female - 5-12 years',
    'ec15': 'Unpaid family workers - Male - 13-15 years',
    'ec16': 'Unpaid family workers - Female - 13-15 years',
    'ec17': 'Unpaid family workers - Male - 16-17 years',
    'ec18': 'Unpaid family workers - Female - 16-17 years',
    'ec19': 'Unpaid family workers - Male - 18-35 years',
    'ec20': 'Unpaid family workers - Female - 18-35 years',
    'ec21': 'Unpaid family workers - Male - 36+ years',
    'ec22': 'Unpaid family workers - Female - 36+ years',
    'eh04_1': 'Net income from the business per month (KSh)',
    'eh01_1': 'Revenue from total sales of goods and services last month (KSh)',
    'eh02_1': 'Value of stocks at beginning of last month (KSh)',
    'eh03_1': 'Value of stocks at end of last month (KSh)',
    'eh15_1': 'Normal monthly revenue from business (KSh)',
    'eh22_1': 'Total turnover of goods and services in 2015 (KSh)',
    'eg01_1': 'Monthly expenditure on Goodwill',
    'eg03_1': 'Monthly expenditure on Rent',
    'eg04_1': 'Monthly expenditure on NSSF/Health Insurance',
    'eg05_1': 'Monthly expenditure on Electricity',
    'eg06_1': 'Monthly expenditure on Water',
    'eg07_1': 'Monthly expenditure on Telephone usage',
    'eg08_1': 'Monthly expenditure on Internet Costs',
    'eg09_1': 'Monthly expenditure on Insurance (Business)',
    'eg10_1': 'Monthly expenditure on Credit (interest and other charges)',
    'eg11_1': 'Monthly expenditure on Salaries and Wages',
    'eg12_1': 'Monthly expenditure on Purchases of business ware',
    'eg13_1': 'Monthly expenditure on Purchases of inputs and raw materials',
    'eg14_1': 'Monthly expenditure on Transport/storage and warehousing',
    'eg15_1': 'Monthly expenditure on Repairs/maintenance',
    'eg16_1': 'Monthly expenditure on Licenses issued',
    'eg17_1': 'Monthly expenditure on Fines',
    'eg18_1': 'Monthly expenditure on Taxes',
    'eg19_1': 'Monthly expenditure on Advertising',
    'eg20_1': 'Monthly expenditure on Product innovation',
    'eg21_1': 'Monthly expenditure on Process innovation',
    'eg22_1': 'Monthly expenditure on Social Responsibility',
    'eg23_1': 'Monthly expenditure on Office supplies',
    'eg24_1': 'Monthly expenditure on Additional Equipment and machinery',
    'eg25_1': 'Monthly expenditure on Other operating costs',
    'el01': 'Initial capital invested in business',
    'el03': 'Additional capital invested after start',
    'el05': 'Approximate current gross worth of business',
    'ej01': 'Main reason for starting business',
    'ej02': 'Main reason for choosing this activity',
    'ej13': 'Sex of business owners',
    'ej14': 'Type of ownership structure',
    'ej15': 'Business registered by registrar of companies',
    'ej17': 'How prices are set',
    'ej18': 'Main source of inputs/purchases',
    'ej19': 'Main buyer of products or services',
    'em01': 'Applied for credit in last three years',
    'em02': 'Amount applied for credit (KSh)',
    'em03': 'Amount received from credit (KSh)',
    'em04': 'Main credit source',
    'em05': 'Main purpose of credit',
    'em13': 'Most serious constraint to business in last year',
    'em14': 'Second most serious constraint to business',
    'em15': 'Third most serious constraint to business',
    'tot_exp': 'Total expenses',
    'total_employees': 'Total number of employees',
    'total_males_emp': 'Total male employees',
    'total_females_emp': 'Total female employees',
    'est_wgt': 'Establishment Weight'
}

# Categorical mappings for better interpretation
CATEGORICAL_MAPPINGS = {
    'ej13': {  # Sex of business owners
        1: 'Male only',
        2: 'Female only', 
        3: 'Both male and female',
        4: 'Mixed ownership'
    },
    'ej14': {  # Type of ownership structure
        1: 'Sole proprietorship',
        2: 'Partnership',
        3: 'Private limited company',
        4: 'Public limited company',
        5: 'Cooperative',
        6: 'Other'
    },
    'ej15': {  # Business registration status
        1: 'Yes, registered',
        2: 'No, not registered'
    },
    'eb04': {  # Site appropriateness for customers
        1: 'Very appropriate',
        2: 'Appropriate',
        3: 'Inappropriate',
        4: 'Very inappropriate'
    },
    'eh09': {  # Business performance ranking
        1: 'Good (above average)',
        2: 'Normal',
        3: 'Bad (below average)'
    }
}

def get_column_description(column_name):
    """Get human-readable description for a column"""
    return DATA_DICTIONARY.get(column_name, column_name)

def get_categorical_mapping(column_name):
    """Get categorical value mappings for a column"""
    return CATEGORICAL_MAPPINGS.get(column_name, {})

def get_key_business_metrics():
    """Return list of key business performance indicators"""
    return [
        'eh04_1',  # Net income
        'eh01_1',  # Revenue
        'eh15_1',  # Normal monthly revenue
        'eh22_1',  # Annual turnover 2015
        'tot_exp',  # Total expenses
        'el01',    # Initial capital
        'el05',    # Current gross worth
        'total_employees'  # Total employees
    ]

def get_expense_categories():
    """Return list of expense category columns"""
    return [col for col in DATA_DICTIONARY.keys() if col.startswith('eg') and col.endswith('_1')]

def get_employee_categories():
    """Return list of employee-related columns"""
    return [col for col in DATA_DICTIONARY.keys() if col.startswith('ec') and col.isdigit() != col[-1].isdigit()]
