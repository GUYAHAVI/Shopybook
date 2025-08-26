import pandas as pd
import os

from google.colab import files
uploaded = files.upload()
# Get the filename from the uploaded dictionary
filename = list(uploaded.keys())[0]
df = pd.read_stata(filename)
df

# Define the target column based on our analysis
target_column = 'eh04_1' # Net income from the business per month

# Separate features (X) and target (y)
X = df.drop(columns=[target_column])
y = df[target_column]

# Display the shapes of X and y to confirm the separation
print("Shape of X:", X.shape)
print("Shape of y:", y.shape)
import numpy as np
from sklearn.impute import SimpleImputer
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline

# Identify columns with missing values
missing_values_cols = X.columns[X.isnull().any()].tolist()
print("Columns with missing values:", missing_values_cols)

# Separate columns by data type
numerical_cols = X.select_dtypes(include=np.number).columns.tolist()
categorical_cols = X.select_dtypes(include='object').columns.tolist()

# Impute missing values
# Numerical columns: fill with mean
numerical_imputer = SimpleImputer(strategy='mean')
X[numerical_cols] = numerical_imputer.fit_transform(X[numerical_cols])

# Categorical columns: fill with mode
categorical_imputer = SimpleImputer(strategy='most_frequent')
X[categorical_cols] = categorical_imputer.fit_transform(X[categorical_cols])

# Verify that missing values have been handled
print("\nMissing values after imputation:", X.isnull().sum().sum())

# Identify categorical and numerical columns again after imputation
numerical_cols = X.select_dtypes(include=np.number).columns.tolist()
categorical_cols = X.select_dtypes(include='object').columns.tolist()

# Create preprocessing pipelines for numerical and categorical features
numerical_transformer = Pipeline(steps=[
    ('scaler', StandardScaler())
])

categorical_transformer = Pipeline(steps=[
    ('onehot', OneHotEncoder(handle_unknown='ignore'))
])

# Create a column transformer to apply different transformations to different columns
preprocessor = ColumnTransformer(
    transformers=[
        ('num', numerical_transformer, numerical_cols),
        ('cat', categorical_transformer, categorical_cols)
    ])

# Apply the preprocessing steps to the features
X_preprocessed = preprocessor.fit_transform(X)

# Convert the preprocessed features back to a DataFrame (optional, but useful for inspection)
# Get the feature names after one-hot encoding
onehot_feature_names = preprocessor.named_transformers_['cat']['onehot'].get_feature_names_out(categorical_cols)
all_feature_names = numerical_cols + list(onehot_feature_names)

X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)

display(X_preprocessed_df.head())

import numpy as np
from sklearn.impute import SimpleImputer
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline

# Separate columns by data type
numerical_cols = X.select_dtypes(include=np.number).columns.tolist()
categorical_cols = X.select_dtypes(include='object').columns.tolist()

# Create preprocessing pipelines for numerical and categorical features
# Numerical pipeline: impute with mean and then scale
numerical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='mean')),
    ('scaler', StandardScaler())
])

# Categorical pipeline: impute with most frequent and then one-hot encode
categorical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='most_frequent')),
    ('onehot', OneHotEncoder(handle_unknown='ignore'))
])

# Create a column transformer to apply different transformations to different columns
preprocessor = ColumnTransformer(
    transformers=[
        ('num', numerical_transformer, numerical_cols),
        ('cat', categorical_transformer, categorical_cols)
    ],
    remainder='passthrough' # Keep other columns (if any) - although not expected here
)

# Apply the preprocessing steps to the features
X_preprocessed = preprocessor.fit_transform(X)

# Get the feature names after one-hot encoding
onehot_feature_names = preprocessor.named_transformers_['cat'].named_steps['onehot'].get_feature_names_out(categorical_cols)
all_feature_names = numerical_cols + list(onehot_feature_names)

X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)

display(X_preprocessed_df.head())
import numpy as np
from sklearn.impute import SimpleImputer
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
import pandas as pd # Import pandas

# Separate columns by data type
numerical_cols = X.select_dtypes(include=np.number).columns.tolist()
categorical_cols = X.select_dtypes(include='object').columns.tolist()

# Convert categorical columns to string type to avoid dtype issues
for col in categorical_cols:
    X[col] = X[col].astype(str)


# Create preprocessing pipelines for numerical and categorical features
# Numerical pipeline: impute with mean and then scale
numerical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='mean')),
    ('scaler', StandardScaler())
])

# Categorical pipeline: impute with most frequent and then one-hot encode
categorical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='most_frequent')),
    ('onehot', OneHotEncoder(handle_unknown='ignore'))
])

# Create a column transformer to apply different transformations to different columns
preprocessor = ColumnTransformer(
    transformers=[
        ('num', numerical_transformer, numerical_cols),
        ('cat', categorical_transformer, categorical_cols)
    ],
    remainder='passthrough' # Keep other columns (if any) - although not expected here
)

# Apply the preprocessing steps to the features
X_preprocessed = preprocessor.fit_transform(X)

# Get the feature names after one-hot encoding
onehot_feature_names = preprocessor.named_transformers_['cat'].named_steps['onehot'].get_feature_names_out(categorical_cols)
all_feature_names = numerical_cols + list(onehot_feature_names)

X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)

display(X_preprocessed_df.head())
# Inspect unique values and data types in categorical columns after string conversion
for col in categorical_cols:
    print(f"Column: {col}, Dtype: {X[col].dtype}")
    print(f"Unique values sample for {col}: {X[col].unique()[:10]}") # Display first 10 unique values

# Re-run the preprocessing pipeline with a potential fix for object dtype issue in sparse matrix
# This might involve identifying and excluding columns that cause the error,
# or trying a different approach for handling them.
# Based on the error and previous attempts, it seems there might be a specific issue
# with how one-hot encoding interacts with the imputed string data in some columns.
# Let's try to exclude the column 'ef03_12' which was mentioned in the warning as it has no non-missing values.
# This might not be the direct cause of the Value Error but it's a good practice to handle such columns.

if 'ef03_12' in numerical_cols:
    numerical_cols.remove('ef03_12')

# Re-create preprocessing pipelines
numerical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='mean')),
    ('scaler', StandardScaler())
])

categorical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='most_frequent')),
    ('onehot', OneHotEncoder(handle_unknown='ignore'))
])

preprocessor = ColumnTransformer(
    transformers=[
        ('num', numerical_transformer, numerical_cols),
        ('cat', categorical_transformer, categorical_cols)
    ],
    remainder='passthrough'
)

# Apply preprocessing
X_preprocessed = preprocessor.fit_transform(X)

# Get feature names and create DataFrame
onehot_feature_names = preprocessor.named_transformers_['cat'].named_steps['onehot'].get_feature_names_out(categorical_cols)
all_feature_names = numerical_cols + list(onehot_feature_names)

X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)

display(X_preprocessed_df.head())
# Exclude identifier columns that are not suitable for one-hot encoding
categorical_cols_to_encode = [col for col in categorical_cols if col not in ['key3', 'key2']]

# Create preprocessing pipelines for numerical and selected categorical features
numerical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='mean')),
    ('scaler', StandardScaler())
])

categorical_transformer = Pipeline(steps=[
    ('imputer', SimpleImputer(strategy='most_frequent')),
    ('onehot', OneHotEncoder(handle_unknown='ignore'))
])

# Create a column transformer with the refined list of categorical columns
preprocessor = ColumnTransformer(
    transformers=[
        ('num', numerical_transformer, numerical_cols),
        ('cat', categorical_transformer, categorical_cols_to_encode)
    ],
    remainder='passthrough' # Keep other columns (like key3, key2) as is for now
)

# Apply the preprocessing steps to the features
# Convert to dense array to avoid scipy.sparse object dtype issue if it arises from onehotencoder output
X_preprocessed = preprocessor.fit_transform(X).todense()


# Get the feature names after one-hot encoding
onehot_feature_names = preprocessor.named_transformers_['cat'].named_steps['onehot'].get_feature_names_out(categorical_cols_to_encode)
# Get the names of the columns that were passed through
passthrough_cols = [col for col in X.columns if col not in numerical_cols and col not in categorical_cols_to_encode]
all_feature_names = numerical_cols + list(onehot_feature_names) + passthrough_cols


X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)

display(X_preprocessed_df.head())
# Isolate and apply the categorical transformation
categorical_transformer.fit(X[categorical_cols_to_encode])
X_categorical_preprocessed_sparse = categorical_transformer.transform(X[categorical_cols_to_encode])

# Check the dtype of the sparse output from the categorical transformer
print("Dtype of categorical preprocessed sparse output:", X_categorical_preprocessed_sparse.dtype)

# Convert the categorical sparse output to a dense array
X_categorical_preprocessed_dense = X_categorical_preprocessed_sparse.todense()

# Apply the numerical transformation
numerical_transformer.fit(X[numerical_cols])
X_numerical_preprocessed = numerical_transformer.transform(X[numerical_cols])

# Concatenate the preprocessed numerical and categorical features (now both dense)
X_preprocessed = np.hstack((X_numerical_preprocessed, X_categorical_preprocessed_dense))

# Get feature names and create DataFrame
onehot_feature_names = categorical_transformer.named_steps['onehot'].get_feature_names_out(categorical_cols_to_encode)
all_feature_names = numerical_cols + list(onehot_feature_names)

X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)

display(X_preprocessed_df.head())
from sklearn.model_selection import train_test_split

# Split the preprocessed data into training and testing sets
X_train, X_test, y_train, y_test = train_test_split(X_preprocessed_df, y, test_size=0.2, random_state=42)

# Print the shapes of the resulting sets
print("Shape of X_train:", X_train.shape)
print("Shape of X_test:", X_test.shape)
print("Shape of y_train:", y_train.shape)
print("Shape of y_test:", y_test.shape)

from sklearn.ensemble import RandomForestRegressor

# Instantiate a RandomForestRegressor object
model = RandomForestRegressor(n_estimators=100, random_state=42, n_jobs=-1)

# Train the model
model.fit(X_train, y_train)
import numpy as np
from sklearn.ensemble import RandomForestRegressor

# Impute missing values in y_train with the mean
y_train_imputed = y_train.fillna(y_train.mean())

# Instantiate a RandomForestRegressor object
model = RandomForestRegressor(n_estimators=100, random_state=42, n_jobs=-1)

# Train the model using the imputed y_train data
model.fit(X_train, y_train_imputed)
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

# Make predictions on the test set
y_pred = model.predict(X_test)

# Calculate evaluation metrics
mae = mean_absolute_error(y_test, y_pred)
mse = mean_squared_error(y_test, y_pred)
rmse = np.sqrt(mse) # Calculate RMSE as well for better interpretability
r2 = r2_score(y_test, y_pred)

# Print the calculated metrics
print(f"Mean Absolute Error (MAE): {mae}")
print(f"Mean Squared Error (MSE): {mse}")
print(f"Root Mean Squared Error (RMSE): {rmse}")
print(f"R-squared (R2): {r2}")
# Impute missing values in y_test with the mean of y_test
y_test_imputed = y_test.fillna(y_test.mean())

# Calculate evaluation metrics using the imputed y_test
mae = mean_absolute_error(y_test_imputed, y_pred)
mse = mean_squared_error(y_test_imputed, y_pred)
rmse = np.sqrt(mse)
r2 = r2_score(y_test_imputed, y_pred)

# Print the calculated metrics
print(f"Mean Absolute Error (MAE): {mae}")
print(f"Mean Squared Error (MSE): {mse}")
print(f"Root Mean Squared Error (RMSE): {rmse}")
print(f"R-squared (R2): {r2}")
import pandas as pd
import matplotlib.pyplot as plt

# Access feature importances from the trained model
feature_importances = model.feature_importances_

# Create a Pandas Series with feature names as index
feature_importance_series = pd.Series(feature_importances, index=X_train.columns)

# Sort feature importances in descending order
sorted_feature_importances = feature_importance_series.sort_values(ascending=False)

# Display the top 20 most important features
print("Top 20 Most Important Features:")
print(sorted_feature_importances.head(20))

# Visualize the top 20 most important features
plt.figure(figsize=(10, 8))
sorted_feature_importances.head(20).plot(kind='bar')
plt.title('Top 20 Most Important Features')
plt.xlabel('Features')
plt.ylabel('Importance')
plt.xticks(rotation=90)
plt.tight_layout()
plt.show()