# -*- coimport pandas as pd
import numpy as np
import os
import matplotlib.pyplot as plt
import pickle
from sklearn.impute import SimpleImputer
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from data_dictionary import get_column_description
"""
Business Analysis ML Model - Local Version with Data Dictionary

Modified for local execution without Google Colab dependencies.
Uses comprehensive data dictionary for better feature understanding and insights.
"""

import pandas as pd
import numpy as np
import os
import matplotlib.pyplot as plt
from sklearn.impute import SimpleImputer
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from data_dictionary import (
    DATA_DICTIONARY, 
    CATEGORICAL_MAPPINGS,
    get_column_description,
    get_key_business_metrics,
    get_expense_categories,
    get_employee_categories
)

def load_data():
    """Load data from local file"""
    # For local execution, specify the path to your data file
    # Replace with the actual path to your .dta file
    filename = '2016 MSME Survey ver. 1.0.dta'  # Update this path as needed
    
    if not os.path.exists(filename):
        print(f"File '{filename}' not found. Please ensure the file exists in the current directory.")
        print("Please update the 'filename' variable with the correct path to your data file.")
        return None
    
    try:
        df = pd.read_stata(filename)
        print(f"Data loaded successfully from '{filename}'")
        print(f"Dataset shape: {df.shape}")
        print("\nFirst few rows:")
        print(df.head())
        
        # Display key business metrics overview
        print("\n" + "="*60)
        print("KEY BUSINESS METRICS OVERVIEW")
        print("="*60)
        key_metrics = get_key_business_metrics()
        for metric in key_metrics:
            if metric in df.columns:
                desc = get_column_description(metric)
                print(f"{metric}: {desc}")
                if df[metric].dtype in ['int64', 'float64']:
                    print(f"  - Mean: {df[metric].mean():.2f}")
                    print(f"  - Median: {df[metric].median():.2f}")
                    print(f"  - Missing values: {df[metric].isnull().sum()}")
                print()
        
        return df
    except Exception as e:
        print(f"Error loading data: {e}")
        return None

def prepare_data(df):
    """Prepare features and target variable"""
    # Define the target column based on analysis
    target_column = 'eh04_1'  # Net income from the business per month
    
    if target_column not in df.columns:
        print(f"Target column '{target_column}' not found in dataset.")
        print("Available columns:", df.columns.tolist())
        return None, None
    
    # Separate features (X) and target (y)
    X = df.drop(columns=[target_column])
    y = df[target_column]
    
    print(f"Shape of X: {X.shape}")
    print(f"Shape of y: {y.shape}")
    
    return X, y

def preprocess_data(X):
    """Preprocess the features"""
    print("Starting data preprocessing...")
    
    # Make a copy to avoid modifying the original data
    X_copy = X.copy()
    
    # Identify columns with missing values
    missing_values_cols = X_copy.columns[X_copy.isnull().any()].tolist()
    print(f"Columns with missing values: {len(missing_values_cols)}")
    
    # Separate columns by data type
    numerical_cols = X_copy.select_dtypes(include=np.number).columns.tolist()
    categorical_cols = X_copy.select_dtypes(include='object').columns.tolist()
    
    print(f"Numerical columns: {len(numerical_cols)}")
    print(f"Categorical columns: {len(categorical_cols)}")
    
    # Remove columns that are entirely NaN for numerical data
    numerical_cols_clean = []
    for col in numerical_cols:
        if not X_copy[col].isnull().all():
            numerical_cols_clean.append(col)
        else:
            print(f"Removing entirely null numerical column: {col}")
    numerical_cols = numerical_cols_clean
    
    # Convert categorical columns to string type to avoid dtype issues
    for col in categorical_cols:
        X_copy[col] = X_copy[col].astype(str)
    
    # Exclude identifier columns that are not suitable for one-hot encoding
    # These are typically columns with unique identifiers
    identifier_cols = ['key3', 'key2'] if any(col in categorical_cols for col in ['key3', 'key2']) else []
    categorical_cols_to_encode = [col for col in categorical_cols if col not in identifier_cols]
    
    print(f"Categorical columns to encode: {len(categorical_cols_to_encode)}")
    
    # Create preprocessing pipelines
    numerical_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='mean')),
        ('scaler', StandardScaler())
    ])
    
    categorical_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='most_frequent')),
        ('onehot', OneHotEncoder(handle_unknown='ignore', sparse_output=False))
    ])
    
    # Apply transformations separately
    print("Applying numerical transformations...")
    if len(numerical_cols) > 0:
        X_numerical_preprocessed = numerical_transformer.fit_transform(X_copy[numerical_cols])
    else:
        X_numerical_preprocessed = np.empty((X_copy.shape[0], 0))
    
    print("Applying categorical transformations...")
    if len(categorical_cols_to_encode) > 0:
        X_categorical_preprocessed = categorical_transformer.fit_transform(X_copy[categorical_cols_to_encode])
        # Get actual feature names after transformation
        onehot_feature_names = categorical_transformer.named_steps['onehot'].get_feature_names_out(categorical_cols_to_encode)
    else:
        X_categorical_preprocessed = np.empty((X_copy.shape[0], 0))
        onehot_feature_names = []
    
    # Combine preprocessed features
    if X_numerical_preprocessed.shape[1] > 0 and X_categorical_preprocessed.shape[1] > 0:
        X_preprocessed = np.hstack((X_numerical_preprocessed, X_categorical_preprocessed))
    elif X_numerical_preprocessed.shape[1] > 0:
        X_preprocessed = X_numerical_preprocessed
    else:
        X_preprocessed = X_categorical_preprocessed
    
    # Create feature names list
    all_feature_names = numerical_cols + list(onehot_feature_names)
    
    # Verify shapes match
    print(f"Data shape: {X_preprocessed.shape}")
    print(f"Feature names count: {len(all_feature_names)}")
    
    # Create DataFrame with proper shape validation
    if X_preprocessed.shape[1] == len(all_feature_names):
        X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=all_feature_names)
    else:
        print(f"Warning: Shape mismatch detected. Using generic column names.")
        generic_names = [f'feature_{i}' for i in range(X_preprocessed.shape[1])]
        X_preprocessed_df = pd.DataFrame(X_preprocessed, columns=generic_names)
    
    print("Preprocessing completed successfully!")
    print(f"Final preprocessed shape: {X_preprocessed_df.shape}")
    print("\nFirst few columns of preprocessed data:")
    print(X_preprocessed_df.iloc[:, :5].head())
    
    return X_preprocessed_df

def split_data(X_preprocessed, y):
    """Split data into training and testing sets"""
    X_train, X_test, y_train, y_test = train_test_split(
        X_preprocessed, y, test_size=0.2, random_state=42
    )
    
    print(f"Shape of X_train: {X_train.shape}")
    print(f"Shape of X_test: {X_test.shape}")
    print(f"Shape of y_train: {y_train.shape}")
    print(f"Shape of y_test: {y_test.shape}")
    
    return X_train, X_test, y_train, y_test

def train_model(X_train, y_train):
    """Train the machine learning model"""
    print("Training RandomForestRegressor model...")
    
    # Handle missing values in target variable
    y_train_imputed = y_train.fillna(y_train.mean())
    print(f"Imputed {y_train.isnull().sum()} missing values in y_train")
    
    # Initialize and train model
    model = RandomForestRegressor(n_estimators=100, random_state=42, n_jobs=-1)
    model.fit(X_train, y_train_imputed)
    
    print("Model training completed!")
    return model

def evaluate_model(model, X_test, y_test):
    """Evaluate the trained model"""
    print("Evaluating model performance...")
    
    # Handle missing values in test target
    y_test_imputed = y_test.fillna(y_test.mean())
    print(f"Imputed {y_test.isnull().sum()} missing values in y_test")
    
    # Make predictions
    y_pred = model.predict(X_test)
    
    # Calculate metrics
    mae = mean_absolute_error(y_test_imputed, y_pred)
    mse = mean_squared_error(y_test_imputed, y_pred)
    rmse = np.sqrt(mse)
    r2 = r2_score(y_test_imputed, y_pred)
    
    print(f"Mean Absolute Error (MAE): {mae:,.2f}")
    print(f"Mean Squared Error (MSE): {mse:,.2f}")
    print(f"Root Mean Squared Error (RMSE): {rmse:,.2f}")
    print(f"R-squared (R2): {r2:.4f}")
    
    return y_pred, y_test_imputed, mae

def analyze_feature_importance(model, X_train):
    """Analyze and visualize feature importance with meaningful descriptions"""
    print("Analyzing feature importance...")
    
    # Get feature importances
    feature_importances = model.feature_importances_
    feature_importance_series = pd.Series(feature_importances, index=X_train.columns)
    sorted_feature_importances = feature_importance_series.sort_values(ascending=False)
    
    print("Top 20 Most Important Features with Descriptions:")
    print("="*80)
    for i, (feature, importance) in enumerate(sorted_feature_importances.head(20).items(), 1):
        description = get_column_description(feature)
        print(f"{i:2d}. {feature} (Importance: {importance:.4f})")
        print(f"    Description: {description}")
        print()
    
    # Create a more readable visualization
    plt.figure(figsize=(14, 10))
    
    # Get top 15 features for better readability
    top_features = sorted_feature_importances.head(15)
    
    # Create labels with descriptions (truncated for readability)
    labels = []
    for feature in top_features.index:
        desc = get_column_description(feature)
        if len(desc) > 40:
            desc = desc[:37] + "..."
        labels.append(f"{feature}\n({desc})")
    
    # Create horizontal bar plot for better label readability
    plt.barh(range(len(top_features)), top_features.values)
    plt.yticks(range(len(top_features)), labels)
    plt.xlabel('Feature Importance')
    plt.title('Top 15 Most Important Features for Predicting Business Net Income', fontsize=14, pad=20)
    plt.gca().invert_yaxis()  # Highest importance at top
    plt.tight_layout()
    plt.show()
    
    return sorted_feature_importances

def generate_business_insights(y_pred, y_test_imputed, mae, sorted_feature_importances):
    """Generate comprehensive business insights and recommendations using data dictionary"""
    print("\n" + "="*80)
    print("COMPREHENSIVE BUSINESS ANALYSIS AND RECOMMENDATIONS")
    print("="*80)
    
    # Create predictions DataFrame
    predictions_df = pd.DataFrame({
        'Actual': y_test_imputed, 
        'Predicted': y_pred
    })
    predictions_df['Difference'] = predictions_df['Actual'] - predictions_df['Predicted']
    predictions_df['Error_Percentage'] = abs(predictions_df['Difference'] / predictions_df['Actual']) * 100
    
    print("\nMODEL PERFORMANCE SUMMARY:")
    print(f"Mean Absolute Error: KSh {mae:,.2f}")
    print(f"Average Prediction Error: {predictions_df['Error_Percentage'].mean():.1f}%")
    print(f"Median Actual Income: KSh {predictions_df['Actual'].median():,.2f}")
    print(f"Median Predicted Income: KSh {predictions_df['Predicted'].median():,.2f}")
    
    # Analyze significant deviations
    significant_deviations = predictions_df[abs(predictions_df['Difference']) > 2 * mae]
    print(f"\nBusinesses with significant prediction deviations (> 2 * MAE):")
    print(f"Count: {len(significant_deviations)} ({len(significant_deviations)/len(predictions_df)*100:.1f}%)")
    
    # DETAILED FEATURE ANALYSIS
    print("\n" + "="*80)
    print("TOP SUCCESS FACTORS ANALYSIS")
    print("="*80)
    
    top_features = sorted_feature_importances.head(10)
    
    # Categorize features for better insights
    financial_features = []
    operational_features = []
    employee_features = []
    other_features = []
    
    for feature, importance in top_features.items():
        description = get_column_description(feature)
        if any(keyword in feature.lower() or keyword in description.lower() 
               for keyword in ['income', 'revenue', 'expense', 'capital', 'credit', 'cost', 'salary', 'wage']):
            financial_features.append((feature, importance, description))
        elif any(keyword in feature.lower() or keyword in description.lower() 
                for keyword in ['employee', 'worker', 'staff', 'ec0', 'ec1', 'ec2', 'ec3']):
            employee_features.append((feature, importance, description))
        elif any(keyword in feature.lower() or keyword in description.lower() 
                for keyword in ['hour', 'day', 'month', 'location', 'business', 'operation']):
            operational_features.append((feature, importance, description))
        else:
            other_features.append((feature, importance, description))
    
    # Print categorized insights
    if financial_features:
        print("\n🏦 FINANCIAL FACTORS:")
        for i, (feature, importance, desc) in enumerate(financial_features, 1):
            print(f"{i}. {feature} (Importance: {importance:.4f})")
            print(f"   📊 {desc}")
            print()
    
    if employee_features:
        print("👥 WORKFORCE FACTORS:")
        for i, (feature, importance, desc) in enumerate(employee_features, 1):
            print(f"{i}. {feature} (Importance: {importance:.4f})")
            print(f"   👤 {desc}")
            print()
    
    if operational_features:
        print("⚙️ OPERATIONAL FACTORS:")
        for i, (feature, importance, desc) in enumerate(operational_features, 1):
            print(f"{i}. {feature} (Importance: {importance:.4f})")
            print(f"   🔧 {desc}")
            print()
    
    if other_features:
        print("📋 OTHER IMPORTANT FACTORS:")
        for i, (feature, importance, desc) in enumerate(other_features, 1):
            print(f"{i}. {feature} (Importance: {importance:.4f})")
            print(f"   📈 {desc}")
            print()
    
    # ACTIONABLE RECOMMENDATIONS
    print("="*80)
    print("ACTIONABLE BUSINESS RECOMMENDATIONS")
    print("="*80)
    
    print("\n💡 IMMEDIATE ACTIONS (High Impact):")
    print("1. REVENUE OPTIMIZATION:")
    print("   • Focus on increasing sales volume and pricing strategies")
    print("   • Diversify revenue streams based on market demand")
    print("   • Implement customer retention programs")
    
    print("\n2. COST MANAGEMENT:")
    print("   • Review and optimize operational expenses")
    print("   • Negotiate better terms with suppliers")
    print("   • Implement cost tracking and budgeting systems")
    
    print("\n3. WORKFORCE OPTIMIZATION:")
    print("   • Align employee numbers with business capacity")
    print("   • Invest in employee training and skill development")
    print("   • Consider gender balance in workforce composition")
    
    print("\n📊 MEDIUM-TERM STRATEGIES:")
    print("1. BUSINESS STRUCTURE:")
    print("   • Consider business registration if not already done")
    print("   • Evaluate ownership structure for tax efficiency")
    print("   • Implement proper record-keeping systems")
    
    print("\n2. CAPITAL INVESTMENT:")
    print("   • Plan strategic capital investments for growth")
    print("   • Explore credit options for business expansion")
    print("   • Maintain optimal inventory levels")
    
    print("\n3. MARKET POSITIONING:")
    print("   • Improve business location accessibility")
    print("   • Enhance customer service quality")
    print("   • Invest in marketing and advertising")
    
    print("\n🎯 LONG-TERM GROWTH:")
    print("1. TECHNOLOGY ADOPTION:")
    print("   • Implement digital payment systems")
    print("   • Use technology for inventory and customer management")
    print("   • Explore e-commerce opportunities")
    
    print("\n2. BUSINESS EXPANSION:")
    print("   • Consider opening additional locations")
    print("   • Explore new product/service lines")
    print("   • Build strategic partnerships")
    
    print("\n3. FINANCIAL PLANNING:")
    print("   • Establish emergency funds")
    print("   • Plan for seasonal variations")
    print("   • Consider business insurance options")
    
    # SPECIFIC RECOMMENDATIONS BASED ON TOP FEATURES
    print("\n" + "="*80)
    print("FEATURE-SPECIFIC RECOMMENDATIONS")
    print("="*80)
    
    for i, (feature, importance) in enumerate(top_features.head(5).items(), 1):
        description = get_column_description(feature)
        print(f"\n{i}. {feature} - {description}")
        print(f"   Importance: {importance:.4f}")
        
        # Provide specific recommendations based on feature type
        if 'revenue' in description.lower() or 'sales' in description.lower():
            print("   💰 Recommendation: Focus on increasing sales through:")
            print("      - Better customer targeting and marketing")
            print("      - Product/service quality improvements")
            print("      - Competitive pricing strategies")
        
        elif 'expense' in description.lower() or 'cost' in description.lower():
            print("   💸 Recommendation: Optimize costs by:")
            print("      - Regular expense audits")
            print("      - Negotiating with suppliers")
            print("      - Eliminating unnecessary expenses")
        
        elif 'employee' in description.lower() or 'worker' in description.lower():
            print("   👥 Recommendation: Optimize workforce by:")
            print("      - Right-sizing team based on business needs")
            print("      - Providing skills training")
            print("      - Implementing performance incentives")
        
        elif 'capital' in description.lower():
            print("   🏦 Recommendation: Manage capital effectively by:")
            print("      - Strategic investment in high-return areas")
            print("      - Maintaining adequate working capital")
            print("      - Exploring financing options for growth")
        
        else:
            print("   📊 Recommendation: Monitor and optimize this factor regularly")
            print("      - Track performance metrics")
            print("      - Benchmark against industry standards")
            print("      - Implement continuous improvement processes")
    
    print("\n--- NEXT STEPS ---")
    print("1. Investigate the specific meanings of top features using the data dictionary")
    print("2. Develop targeted intervention strategies for businesses with large prediction errors")
    print("3. Create personalized recommendations based on individual business profiles")
    print("4. Consider model refinement with additional features or advanced algorithms")

def main():
    """Main execution function"""
    print("Starting Business Analysis ML Model (Local Version)")
    print("="*60)
    
    # Load data
    df = load_data()
    if df is None:
        return
    
    # Prepare data
    X, y = prepare_data(df)
    if X is None or y is None:
        return
    
    # Preprocess data
    X_preprocessed = preprocess_data(X)
    if X_preprocessed is None:
        return
    
    # Split data
    X_train, X_test, y_train, y_test = split_data(X_preprocessed, y)
    
    # Train model
    model = train_model(X_train, y_train)
    
    # Save the trained model for the advisor
    try:
        with open('trained_model.pkl', 'wb') as f:
            pickle.dump(model, f)
        print("✅ Model saved successfully for the AI Business Advisor!")
    except Exception as e:
        print(f"⚠️ Could not save model: {e}")
    
    # Evaluate model
    y_pred, y_test_imputed, mae = evaluate_model(model, X_test, y_test)
    
    # Analyze feature importance
    sorted_feature_importances = analyze_feature_importance(model, X_train)
    
    # Generate insights
    generate_business_insights(y_pred, y_test_imputed, mae, sorted_feature_importances)
    
    print("\nAnalysis completed successfully!")
    print("\n🤖 To ask specific business questions, run:")
    print("python business_advisor.py")

if __name__ == "__main__":
    main()
